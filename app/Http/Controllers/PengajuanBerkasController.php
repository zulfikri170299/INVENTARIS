<?php

namespace App\Http\Controllers;

use App\Models\PengajuanBerkas;
use App\Models\PersyaratanBerkas;
use App\Models\DokumenPengajuan;
use App\Models\RiwayatPengajuan;
use App\Models\User;
use App\Notifications\PengajuanStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengajuanBerkasController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = PengajuanBerkas::with(['user', 'satker', 'riwayat']);

        // Admin Satker hanya lihat pengajuan milik satker sendiri
        if (!in_array($user->role, ['Super Admin', 'Super Admin 2', 'Pimpinan'])) {
            $query->where('satker_id', $user->satker_id);
        }

        // Filter kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhereHas('satker', fn($sq) => $sq->where('nama_satker', 'like', "%{$search}%"));
            });
        }

        $perPage = $request->input('per_page', 10);
        $pengajuan = $query->orderBy('updated_at', 'desc')->paginate($perPage)->withQueryString();

        return view('pengajuan.index', compact('pengajuan'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        if (in_array($user->role, ['Super Admin', 'Super Admin 2'])) {
            return redirect()->route('pengajuan-berkas.index')
                ->with('error', 'Super Admin tidak bisa membuat pengajuan.');
        }

        $kategori = $request->get('kategori', 'penghapusan');
        $persyaratan = PersyaratanBerkas::where('kategori', $kategori)
            ->orderBy('urutan')
            ->get();

        return view('pengajuan.create', compact('kategori', 'persyaratan'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if (in_array($user->role, ['Super Admin', 'Super Admin 2'])) {
            return redirect()->route('pengajuan-berkas.index')
                ->with('error', 'Super Admin tidak bisa membuat pengajuan.');
        }

        $request->validate([
            'kategori' => 'required|in:penghapusan,penetapan_status',
            'judul' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        // Validate required persyaratan files
        $persyaratan = PersyaratanBerkas::where('kategori', $request->kategori)
            ->where('wajib', true)
            ->get();

        foreach ($persyaratan as $syarat) {
            if (!$request->hasFile("dokumen_{$syarat->id}")) {
                return back()->withInput()
                    ->withErrors(["dokumen_{$syarat->id}" => "Dokumen '{$syarat->nama_persyaratan}' wajib diupload."]);
            }
        }

        $pengajuan = PengajuanBerkas::create([
            'kategori' => $request->kategori,
            'user_id' => $user->id,
            'satker_id' => $user->satker_id,
            'judul' => $request->judul,
            'keterangan' => $request->keterangan,
            'status' => 'diajukan',
        ]);

        // Upload semua dokumen
        $allPersyaratan = PersyaratanBerkas::where('kategori', $request->kategori)->get();
        foreach ($allPersyaratan as $syarat) {
            if ($request->hasFile("dokumen_{$syarat->id}")) {
                $file = $request->file("dokumen_{$syarat->id}");
                $path = $file->store("pengajuan/{$pengajuan->id}", 'public');

                DokumenPengajuan::create([
                    'pengajuan_berkas_id' => $pengajuan->id,
                    'persyaratan_berkas_id' => $syarat->id,
                    'file_path' => $path,
                    'nama_file' => $file->getClientOriginalName(),
                ]);
            }
        }

        // Buat riwayat
        RiwayatPengajuan::create([
            'pengajuan_berkas_id' => $pengajuan->id,
            'user_id' => $user->id,
            'status' => 'diajukan',
            'catatan' => 'Pengajuan berkas baru telah dibuat.',
        ]);

        $superAdmins = User::whereIn('role', ['Super Admin', 'Super Admin 2'])->get();
        foreach ($superAdmins as $admin) {
            $admin->notify(new PengajuanStatusNotification($pengajuan, 'Pengajuan Baru', "Ada pengajuan baru dari {$user->name}."));
        }

        return redirect()->route('pengajuan-berkas.show', $pengajuan->id)
            ->with('success', 'Pengajuan berkas berhasil dibuat.');
    }

    public function show($id)
    {
        $user = auth()->user();
        $pengajuan = PengajuanBerkas::with(['user', 'satker', 'dokumen.persyaratan', 'riwayat.user'])->findOrFail($id);

        // Admin Satker hanya bisa lihat milik satker sendiri
        if (!in_array($user->role, ['Super Admin', 'Super Admin 2']) && $pengajuan->satker_id !== $user->satker_id) {
            abort(403, 'Anda tidak memiliki akses ke pengajuan ini.');
        }

        $persyaratan = PersyaratanBerkas::where('kategori', $pengajuan->kategori)
            ->orderBy('urutan')
            ->get();

        return view('pengajuan.show', compact('pengajuan', 'persyaratan'));
    }

    public function terima($id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['Super Admin', 'Super Admin 2'])) {
            abort(403);
        }

        $pengajuan = PengajuanBerkas::findOrFail($id);

        if ($pengajuan->status !== 'diajukan') {
            return back()->with('error', 'Pengajuan tidak dalam status yang bisa diterima.');
        }

        $pengajuan->update(['status' => 'diterima', 'catatan_super_admin' => null]);

        RiwayatPengajuan::create([
            'pengajuan_berkas_id' => $pengajuan->id,
            'user_id' => $user->id,
            'status' => 'diterima',
            'catatan' => 'Berkas telah diterima dan diverifikasi.',
        ]);

        $pengajuan->user->notify(new PengajuanStatusNotification($pengajuan, 'Pengajuan Diterima', "Pengajuan '{$pengajuan->judul}' telah diterima."));

        return back()->with('success', 'Pengajuan berhasil diterima.');
    }

    public function proses($id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['Super Admin', 'Super Admin 2'])) {
            abort(403);
        }

        $pengajuan = PengajuanBerkas::findOrFail($id);

        if ($pengajuan->status !== 'diterima') {
            return back()->with('error', 'Pengajuan belum diterima, tidak bisa diproses.');
        }

        $pengajuan->update(['status' => 'diproses']);

        RiwayatPengajuan::create([
            'pengajuan_berkas_id' => $pengajuan->id,
            'user_id' => $user->id,
            'status' => 'diproses',
            'catatan' => 'Berkas sedang dalam proses pengerjaan.',
        ]);

        $pengajuan->user->notify(new PengajuanStatusNotification($pengajuan, 'Pengajuan Diproses', "Pengajuan '{$pengajuan->judul}' sedang diproses."));

        return back()->with('success', 'Status berhasil diubah: Sedang Diproses.');
    }

    public function kembalikan(Request $request, $id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['Super Admin', 'Super Admin 2'])) {
            abort(403);
        }

        $request->validate([
            'catatan' => 'required|string',
            'revisi_dokumen' => 'nullable|array',
            'revisi_dokumen.*' => 'exists:dokumen_pengajuan,id',
        ]);

        $pengajuan = PengajuanBerkas::findOrFail($id);

        if (!in_array($pengajuan->status, ['diajukan', 'diterima', 'diproses'])) {
            return back()->with('error', 'Pengajuan tidak dalam status yang bisa dikembalikan.');
        }

        $pengajuan->update([
            'status' => 'dikembalikan',
            'catatan_super_admin' => $request->catatan,
        ]);

        // Reset semua dokumen menjadi tidak butuh_revisi
        DokumenPengajuan::where('pengajuan_berkas_id', $pengajuan->id)->update(['butuh_revisi' => false]);
        
        // Set butuh_revisi untuk dokumen yang dicentang
        if ($request->has('revisi_dokumen')) {
            DokumenPengajuan::whereIn('id', $request->revisi_dokumen)->update(['butuh_revisi' => true]);
        }

        RiwayatPengajuan::create([
            'pengajuan_berkas_id' => $pengajuan->id,
            'user_id' => $user->id,
            'status' => 'dikembalikan',
            'catatan' => $request->catatan,
        ]);

        $pengajuan->user->notify(new PengajuanStatusNotification($pengajuan, 'Pengajuan Dikembalikan', "Pengajuan '{$pengajuan->judul}' dikembalikan dengan catatan."));

        return back()->with('success', 'Pengajuan telah dikembalikan dengan catatan.');
    }

    public function perbaiki(Request $request, $id)
    {
        $user = auth()->user();
        $pengajuan = PengajuanBerkas::findOrFail($id);

        if ($pengajuan->satker_id !== $user->satker_id) {
            abort(403);
        }

        if ($pengajuan->status !== 'dikembalikan') {
            return back()->with('error', 'Pengajuan tidak dalam status dikembalikan.');
        }

        // Upload ulang dokumen yang diperbaiki
        $persyaratan = PersyaratanBerkas::where('kategori', $pengajuan->kategori)->get();
        $hasNewFile = false;

        foreach ($persyaratan as $syarat) {
            if ($request->hasFile("dokumen_{$syarat->id}")) {
                $hasNewFile = true;
                $file = $request->file("dokumen_{$syarat->id}");

                // Hapus dokumen lama untuk persyaratan ini
                $oldDok = DokumenPengajuan::where('pengajuan_berkas_id', $pengajuan->id)
                    ->where('persyaratan_berkas_id', $syarat->id)
                    ->first();

                if ($oldDok) {
                    Storage::disk('public')->delete($oldDok->file_path);
                    $oldDok->delete();
                }

                $path = $file->store("pengajuan/{$pengajuan->id}", 'public');
                DokumenPengajuan::create([
                    'pengajuan_berkas_id' => $pengajuan->id,
                    'persyaratan_berkas_id' => $syarat->id,
                    'file_path' => $path,
                    'nama_file' => $file->getClientOriginalName(),
                ]);
            }
        }

        $pengajuan->update([
            'status' => 'diajukan',
            'catatan_super_admin' => null,
        ]);

        RiwayatPengajuan::create([
            'pengajuan_berkas_id' => $pengajuan->id,
            'user_id' => $user->id,
            'status' => 'diajukan',
            'catatan' => 'Berkas telah diperbaiki dan diajukan kembali.',
        ]);

        $superAdmins = User::whereIn('role', ['Super Admin', 'Super Admin 2'])->get();
        foreach ($superAdmins as $admin) {
            $admin->notify(new PengajuanStatusNotification($pengajuan, 'Pengajuan Diperbaiki', "Pengajuan '{$pengajuan->judul}' telah diperbaiki."));
        }

        return back()->with('success', 'Berkas berhasil diperbaiki dan diajukan kembali.');
    }

    public function naikKeKapolda($id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['Super Admin', 'Super Admin 2'])) {
            abort(403);
        }

        $pengajuan = PengajuanBerkas::findOrFail($id);

        if ($pengajuan->status !== 'diproses') {
            return back()->with('error', 'Pengajuan harus diproses terlebih dahulu sebelum naik ke Kapolda.');
        }

        $pengajuan->update(['status' => 'naik_ke_kapolda']);

        RiwayatPengajuan::create([
            'pengajuan_berkas_id' => $pengajuan->id,
            'user_id' => $user->id,
            'status' => 'naik_ke_kapolda',
            'catatan' => 'Berkas telah dikirim ke Kapolda.',
        ]);

        $pengajuan->user->notify(new PengajuanStatusNotification($pengajuan, 'Naik ke Kapolda', "Pengajuan '{$pengajuan->judul}' diteruskan ke Kapolda."));

        return back()->with('success', 'Status berhasil diubah: Berkas naik ke Kapolda.');
    }

    public function tandatangan($id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['Super Admin', 'Super Admin 2'])) {
            abort(403);
        }

        $pengajuan = PengajuanBerkas::findOrFail($id);

        if ($pengajuan->status !== 'naik_ke_kapolda') {
            return back()->with('error', 'Berkas harus sudah naik ke Kapolda.');
        }

        $pengajuan->update(['status' => 'ditandatangani']);

        RiwayatPengajuan::create([
            'pengajuan_berkas_id' => $pengajuan->id,
            'user_id' => $user->id,
            'status' => 'ditandatangani',
            'catatan' => 'Berkas telah ditandatangani oleh Kapolda.',
        ]);

        $pengajuan->user->notify(new PengajuanStatusNotification($pengajuan, 'Ditandatangani', "Pengajuan '{$pengajuan->judul}' telah ditandatangani Kapolda."));

        return back()->with('success', 'Status berhasil diubah: Ditandatangani Kapolda.');
    }

    public function kirimBerkasFinal(Request $request, $id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['Super Admin', 'Super Admin 2'])) {
            abort(403);
        }

        $request->validate([
            'berkas_final' => 'required|file|mimes:pdf|max:10240',
        ]);

        $pengajuan = PengajuanBerkas::findOrFail($id);

        if ($pengajuan->status !== 'ditandatangani') {
            return back()->with('error', 'Berkas harus sudah ditandatangani Kapolda.');
        }

        $file = $request->file('berkas_final');
        $path = $file->store("pengajuan/{$pengajuan->id}/final", 'public');

        $pengajuan->update([
            'status' => 'selesai',
            'berkas_final' => $path,
        ]);

        RiwayatPengajuan::create([
            'pengajuan_berkas_id' => $pengajuan->id,
            'user_id' => $user->id,
            'status' => 'selesai',
            'catatan' => 'Berkas final telah dikirim ke Admin Satker.',
        ]);

        $pengajuan->user->notify(new PengajuanStatusNotification($pengajuan, 'Pengajuan Selesai', "Berkas final untuk '{$pengajuan->judul}' telah terbit."));

        return back()->with('success', 'Berkas final berhasil dikirim. Pengajuan selesai.');
    }

    public function downloadBerkasFinal($id)
    {
        $user = auth()->user();
        $pengajuan = PengajuanBerkas::findOrFail($id);

        if (!in_array($user->role, ['Super Admin', 'Super Admin 2']) && $pengajuan->satker_id !== $user->satker_id) {
            abort(403);
        }

        if (!$pengajuan->berkas_final) {
            return back()->with('error', 'Berkas final belum tersedia.');
        }

        return Storage::disk('public')->download($pengajuan->berkas_final, 'berkas-final-' . $pengajuan->judul . '.pdf');
    }

    public function previewBerkasFinal($id)
    {
        $user = auth()->user();
        $pengajuan = PengajuanBerkas::findOrFail($id);

        if (!in_array($user->role, ['Super Admin', 'Super Admin 2']) && $pengajuan->satker_id !== $user->satker_id) {
            abort(403);
        }

        if (!$pengajuan->berkas_final || !Storage::disk('public')->exists($pengajuan->berkas_final)) {
            abort(404);
        }

        return Storage::disk('public')->response($pengajuan->berkas_final);
    }

    public function downloadDokumen($id)
    {
        $dokumen = DokumenPengajuan::with('pengajuanBerkas')->findOrFail($id);
        $user = auth()->user();

        if (!in_array($user->role, ['Super Admin', 'Super Admin 2']) && $dokumen->pengajuanBerkas->satker_id !== $user->satker_id) {
            abort(403);
        }

        return Storage::disk('public')->download($dokumen->file_path, $dokumen->nama_file);
    }

    public function previewDokumen($id)
    {
        $dokumen = DokumenPengajuan::with('pengajuanBerkas')->findOrFail($id);
        $user = auth()->user();

        if (!in_array($user->role, ['Super Admin', 'Super Admin 2']) && $dokumen->pengajuanBerkas->satker_id !== $user->satker_id) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($dokumen->file_path)) {
            abort(404);
        }

        return Storage::disk('public')->response($dokumen->file_path);
    }

    public function annotateDokumen($id)
    {
        $dokumen = DokumenPengajuan::with('pengajuanBerkas')->findOrFail($id);
        $user = auth()->user();

        // Hanya Super Admin yang boleh koreksi, dan hanya saat status masih memungkinkan
        if (!in_array($user->role, ['Super Admin', 'Super Admin 2'])) {
            abort(403);
        }

        if (!in_array($dokumen->pengajuanBerkas->status, ['diajukan', 'diterima', 'diproses', 'dikembalikan'])) {
            return back()->with('error', 'Status pengajuan tidak memungkinkan untuk koreksi dokumen.');
        }

        if (!Storage::disk('public')->exists($dokumen->file_path)) {
            abort(404, 'File PDF tidak ditemukan.');
        }

        return view('pengajuan.annotate', compact('dokumen'));
    }

    public function saveAnnotation(Request $request, $id)
    {
        $dokumen = DokumenPengajuan::with('pengajuanBerkas')->findOrFail($id);
        $user = auth()->user();

        if (!in_array($user->role, ['Super Admin', 'Super Admin 2'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'annotated_pdf' => 'required|file|mimes:pdf|max:10240', // Max 10MB
        ]);

        $file = $request->file('annotated_pdf');
        
        // Simpan file baru dan timpa yang lama
        $path = $file->storeAs(
            dirname($dokumen->file_path),
            basename($dokumen->file_path),
            'public'
        );

        // Kasih catatan history? Bisa juga tidak wajib.
        return response()->json([
            'success' => true,
            'message' => 'Dokumen berhasil dikoreksi.',
            'redirect' => route('pengajuan-berkas.show', $dokumen->pengajuanBerkas->id)
        ]);
    }
}
