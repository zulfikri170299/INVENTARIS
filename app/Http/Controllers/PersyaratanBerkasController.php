<?php

namespace App\Http\Controllers;

use App\Models\PersyaratanBerkas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PersyaratanBerkasController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['Super Admin', 'Super Admin 2'])) {
            abort(403, 'Hanya Super Admin yang dapat mengelola persyaratan.');
        }

        $kategori = $request->get('kategori', 'penghapusan');
        $persyaratan = PersyaratanBerkas::where('kategori', $kategori)
            ->orderBy('urutan')
            ->get();

        return view('persyaratan.index', compact('persyaratan', 'kategori'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['Super Admin', 'Super Admin 2'])) {
            abort(403);
        }

        $kategori = $request->get('kategori', 'penghapusan');
        return view('persyaratan.create', compact('kategori'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['Super Admin', 'Super Admin 2'])) {
            abort(403);
        }

        $request->validate([
            'kategori' => 'required|in:penghapusan,penetapan_status',
            'nama_persyaratan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'wajib' => 'boolean',
            'urutan' => 'integer|min:0',
            'file_contoh' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
        ]);

        $data = [
            'kategori' => $request->kategori,
            'nama_persyaratan' => $request->nama_persyaratan,
            'deskripsi' => $request->deskripsi,
            'wajib' => $request->boolean('wajib', true),
            'urutan' => $request->urutan ?? 0,
        ];

        if ($request->hasFile('file_contoh')) {
            $file = $request->file('file_contoh');
            $path = $file->store('persyaratan/contoh', 'public');
            $data['file_contoh'] = $path;
            $data['nama_file_contoh'] = $file->getClientOriginalName();
        }

        PersyaratanBerkas::create($data);

        return redirect()->route('persyaratan-berkas.index', ['kategori' => $request->kategori])
            ->with('success', 'Persyaratan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['Super Admin', 'Super Admin 2'])) {
            abort(403);
        }

        $persyaratan = PersyaratanBerkas::findOrFail($id);
        return view('persyaratan.edit', compact('persyaratan'));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['Super Admin', 'Super Admin 2'])) {
            abort(403);
        }

        $request->validate([
            'nama_persyaratan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'wajib' => 'boolean',
            'urutan' => 'integer|min:0',
            'file_contoh' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
        ]);

        $persyaratan = PersyaratanBerkas::findOrFail($id);
        
        $data = [
            'nama_persyaratan' => $request->nama_persyaratan,
            'deskripsi' => $request->deskripsi,
            'wajib' => $request->boolean('wajib', true),
            'urutan' => $request->urutan ?? 0,
        ];

        if ($request->hasFile('file_contoh')) {
            // Hapus file lama jika ada
            if ($persyaratan->file_contoh) {
                Storage::disk('public')->delete($persyaratan->file_contoh);
            }

            $file = $request->file('file_contoh');
            $path = $file->store('persyaratan/contoh', 'public');
            $data['file_contoh'] = $path;
            $data['nama_file_contoh'] = $file->getClientOriginalName();
        }

        $persyaratan->update($data);

        return redirect()->route('persyaratan-berkas.index', ['kategori' => $persyaratan->kategori])
            ->with('success', 'Persyaratan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['Super Admin', 'Super Admin 2'])) {
            abort(403);
        }

        $persyaratan = PersyaratanBerkas::findOrFail($id);
        $kategori = $persyaratan->kategori;

        // Hapus file contoh jika ada
        if ($persyaratan->file_contoh) {
            Storage::disk('public')->delete($persyaratan->file_contoh);
        }

        $persyaratan->delete();

        return redirect()->route('persyaratan-berkas.index', ['kategori' => $kategori])
            ->with('success', 'Persyaratan berhasil dihapus.');
    }

    public function downloadContoh($id)
    {
        $persyaratan = PersyaratanBerkas::findOrFail($id);

        if (!$persyaratan->file_contoh) {
            return back()->with('error', 'File contoh belum tersedia.');
        }

        if (!Storage::disk('public')->exists($persyaratan->file_contoh)) {
            return back()->with('error', 'File contoh tidak ditemukan di server.');
        }

        return Storage::disk('public')->download($persyaratan->file_contoh, $persyaratan->nama_file_contoh);
    }
}
