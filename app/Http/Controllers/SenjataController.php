<?php

namespace App\Http\Controllers;

use App\Models\Senjata;
use App\Models\Satker;
use App\Imports\SenjataImport;
use App\Exports\SenjataTemplateExport;
use App\Exports\SenjataExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\LogActivity;

class SenjataController extends Controller
{
    use LogActivity;
    private function getFilteredQuery(Request $request, $defaultStatus = null)
    {
        $query = Senjata::with('satker');

        if (auth()->user()->satker_id && !in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2', 'Pimpinan'])) {
            $query->where('satker_id', auth()->user()->satker_id);
        }

        // Handle Status Penyimpanan (Default context)
        $status = $request->input('status_penyimpanan', $defaultStatus);
        if ($status) {
            $query->where('status_penyimpanan', $status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('jenis_senpi', 'like', '%' . $search . '%')
                  ->orWhere('nup', 'like', '%' . $search . '%')
                  ->orWhere('no_senpi', 'like', '%' . $search . '%')
                  ->orWhere('penanggung_jawab', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('satker_id')) {
            $query->where('satker_id', $request->satker_id);
        }

        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }

        if ($request->filled('laras')) {
            $query->where('laras', $request->laras);
        }

        if ($request->filled('masa_simsa')) {
            $today = now()->toDateString();
            $thirtyDaysFromNow = now()->addDays(30)->toDateString();

            if ($request->masa_simsa === 'Aktif') {
                $query->where('masa_berlaku_simsa', '>=', $today);
            } elseif ($request->masa_simsa === 'Akan Habis') {
                $query->whereBetween('masa_berlaku_simsa', [$today, $thirtyDaysFromNow]);
            } elseif ($request->masa_simsa === 'Habis') {
                $query->where('masa_berlaku_simsa', '<', $today);
            }
        }

        return $query->latest();
    }

    public function index(Request $request)
    {
        $query = $this->getFilteredQuery($request, 'Gudang');
        $perPage = $request->input('per_page', 10);
        $senjatas = $query->paginate($perPage)->withQueryString();
        $satkers = Satker::all();

        // Ambil jenis amunisi beserta total stoknya sesuai satker yang sedang login
        $amunisiQuery = \App\Models\Amunisi::select('jenis_amunisi')
            ->selectRaw('SUM(jumlah) as total_stok')
            ->groupBy('jenis_amunisi');

        if (auth()->user()->satker_id && !in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2', 'Pimpinan'])) {
            $amunisiQuery->where('satker_id', auth()->user()->satker_id);
        }
        $availableAmunisi = $amunisiQuery->orderBy('jenis_amunisi')->get();

        return view('senjata.index', compact('senjatas', 'satkers', 'availableAmunisi'));
    }

    public function pembawa(Request $request)
    {
        $query = $this->getFilteredQuery($request, 'Personel');
        $perPage = $request->input('per_page', 10);
        $senjatas = $query->paginate($perPage)->withQueryString();
        $satkers = Satker::all();

        // Ambil jenis amunisi beserta total stoknya sesuai satker yang sedang login
        $amunisiQuery = \App\Models\Amunisi::select('jenis_amunisi')
            ->selectRaw('SUM(jumlah) as total_stok')
            ->groupBy('jenis_amunisi');

        if (auth()->user()->satker_id && !in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2', 'Pimpinan'])) {
            $amunisiQuery->where('satker_id', auth()->user()->satker_id);
        }
        $availableAmunisi = $amunisiQuery->orderBy('jenis_amunisi')->get();

        return view('senjata.pembawa', compact('senjatas', 'satkers', 'availableAmunisi'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'satker_id' => 'required|exists:satkers,id',
            'jenis_senpi' => 'required|string',
            'laras' => 'nullable|in:Panjang,Pendek',
            'nup' => 'nullable|string',
            'no_senpi' => 'nullable|string',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'penanggung_jawab' => 'nullable|string',
            'nrp' => 'nullable|string',
            'status_penyimpanan' => 'nullable|in:Gudang,Personel,Dipinjamkan',
            'masa_berlaku_simsa' => 'nullable|date',
            'jenis_amunisi_dibawa' => 'nullable|string',
            'jumlah_amunisi_dibawa' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        if (auth()->user()->satker_id && !in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2', 'Pimpinan'])) {
            $validated['satker_id'] = auth()->user()->satker_id;
        }

        // Validasi stok amunisi di sisi server
        if ($request->status_penyimpanan === 'Personel' && $request->jenis_amunisi_dibawa && $request->jumlah_amunisi_dibawa > 0) {
            $totalStok = \App\Models\Amunisi::where('satker_id', $validated['satker_id'])
                ->where('jenis_amunisi', $request->jenis_amunisi_dibawa)
                ->sum('jumlah');

            if ($request->jumlah_amunisi_dibawa > $totalStok) {
                return back()->withErrors(['jumlah_amunisi_dibawa' => 'Jumlah amunisi (' . $request->jumlah_amunisi_dibawa . ') melebihi stok yang tersedia (' . $totalStok . ') untuk jenis ' . $request->jenis_amunisi_dibawa])->withInput();
            }
        }

        $senjata = Senjata::create($validated);

        // Amunisi Logic: Decrement warehouse and log history
        if ($senjata->status_penyimpanan === 'Personel' && $senjata->jenis_amunisi_dibawa && $senjata->jumlah_amunisi_dibawa > 0) {
            $amunisi = \App\Models\Amunisi::where('satker_id', $senjata->satker_id)
                ->where('jenis_amunisi', $senjata->jenis_amunisi_dibawa)
                ->where('status_penyimpanan', 'Gudang')
                ->first();

            if ($amunisi) {
                $amunisi->decrement('jumlah', $senjata->jumlah_amunisi_dibawa);
                
                \App\Models\AmunisiHistory::create([
                    'satker_id' => $senjata->satker_id,
                    'nama_personel' => $senjata->penanggung_jawab ?? '-',
                    'pangkat_nrp' => $senjata->nrp ?? '-',
                    'jenis_amunisi' => $senjata->jenis_amunisi_dibawa,
                    'jumlah' => -$senjata->jumlah_amunisi_dibawa, // Negative for out
                    'tanggal' => now(),
                    'keterangan' => 'Diberikan bersama senjata ' . $senjata->jenis_senpi . ' (NUP: ' . ($senjata->nup ?? '-') . ')',
                ]);
            }
        }

        $this->logActivity('Tambah Senjata', 'Menambahkan senjata baru: ' . $senjata->jenis_senpi . ' (NUP: ' . ($senjata->nup ?? '-') . ')', 'Senjata');

        return back()->with('success', 'Data senjata berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $senjata = Senjata::findOrFail($id);
        
        $validated = $request->validate([
            'satker_id' => 'required|exists:satkers,id',
            'jenis_senpi' => 'required|string',
            'laras' => 'nullable|in:Panjang,Pendek',
            'nup' => 'nullable|string',
            'no_senpi' => 'nullable|string',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'penanggung_jawab' => 'nullable|string',
            'nrp' => 'nullable|string',
            'status_penyimpanan' => 'nullable|in:Gudang,Personel,Dipinjamkan',
            'masa_berlaku_simsa' => 'nullable|date',
            'jenis_amunisi_dibawa' => 'nullable|string',
            'jumlah_amunisi_dibawa' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        if (auth()->user()->satker_id && !in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2', 'Pimpinan'])) {
            $validated['satker_id'] = auth()->user()->satker_id;
        }

        // Validasi stok amunisi di sisi server
        if ($request->status_penyimpanan === 'Personel' && $request->jenis_amunisi_dibawa && $request->jumlah_amunisi_dibawa > 0) {
            $warehouseStok = \App\Models\Amunisi::where('satker_id', $validated['satker_id'])
                ->where('jenis_amunisi', $request->jenis_amunisi_dibawa)
                ->where('status_penyimpanan', 'Gudang')
                ->sum('jumlah');

            $currentAssigned = ($senjata->jenis_amunisi_dibawa === $request->jenis_amunisi_dibawa) 
                ? ($senjata->jumlah_amunisi_dibawa ?? 0) 
                : 0;

            $totalAllowed = $warehouseStok + $currentAssigned;

            if ($request->jumlah_amunisi_dibawa > $totalAllowed) {
                return back()->withErrors(['jumlah_amunisi_dibawa' => 'Jumlah amunisi (' . $request->jumlah_amunisi_dibawa . ') melebihi stok yang tersedia (' . $totalAllowed . ') untuk jenis ' . $request->jenis_amunisi_dibawa])->withInput();
            }
        }

        \Log::debug("Updating Senjata ID: " . $id, $validated);
        
        // Auto clear amunisi type if qty is 0
        if (isset($validated['jumlah_amunisi_dibawa']) && $validated['jumlah_amunisi_dibawa'] <= 0) {
            $validated['jenis_amunisi_dibawa'] = null;
        }
        
        $oldAmunisiType = $senjata->jenis_amunisi_dibawa;
        $oldAmunisiQty = $senjata->jumlah_amunisi_dibawa ?? 0;
        $oldStatus = $senjata->status_penyimpanan;

        $senjata->update($validated);

        $newAmunisiType = $senjata->jenis_amunisi_dibawa;
        $newAmunisiQty = $senjata->jumlah_amunisi_dibawa ?? 0;
        $newStatus = $senjata->status_penyimpanan;

        // Logic for Amunisi Adjustment
        if ($oldStatus === 'Personel' || $newStatus === 'Personel') {
            
            // 1. If status changed from Personel to something else, return all old amunisi
            if ($oldStatus === 'Personel' && $newStatus !== 'Personel') {
                if ($oldAmunisiType && $oldAmunisiQty > 0) {
                    $this->returnAmunisiToGudang($senjata, $oldAmunisiType, $oldAmunisiQty, 'Pengembalian (Senjata ditarik dari Personel)');
                }
            } 
            // 2. If status changed to Personel, or qty/type changed while in Personel
            else if ($newStatus === 'Personel') {
                if ($oldStatus !== 'Personel') {
                    // New assignment
                    if ($newAmunisiType && $newAmunisiQty > 0) {
                        $this->takeAmunisiFromGudang($senjata, $newAmunisiType, $newAmunisiQty, 'Pemberian (Status senjata berubah ke Personel)');
                    }
                } else {
                    // Stayed in Personel, but maybe type or qty changed
                    if ($oldAmunisiType !== $newAmunisiType) {
                        // Type changed: return old, take new
                        if ($oldAmunisiType && $oldAmunisiQty > 0) {
                            $this->returnAmunisiToGudang($senjata, $oldAmunisiType, $oldAmunisiQty, 'Pengembalian (Jenis amunisi diubah)');
                        }
                        if ($newAmunisiType && $newAmunisiQty > 0) {
                            $this->takeAmunisiFromGudang($senjata, $newAmunisiType, $newAmunisiQty, 'Pemberian (Jenis amunisi diubah)');
                        }
                    } else if ($oldAmunisiQty !== $newAmunisiQty) {
                        // Quantity changed
                        $diff = $newAmunisiQty - $oldAmunisiQty;
                        if ($diff > 0) {
                            $this->takeAmunisiFromGudang($senjata, $newAmunisiType, $diff, 'Pemberian Tambahan (Penyesuaian jumlah)');
                        } else {
                            $this->returnAmunisiToGudang($senjata, $newAmunisiType, abs($diff), 'Pengembalian (Penyesuaian jumlah)');
                        }
                    }
                }
            }
        }

        $this->logActivity('Update Senjata', 'Memperbarui data senjata: ' . $senjata->jenis_senpi . ' (NUP: ' . ($senjata->nup ?? '-') . ')', 'Senjata');
        
        $route = $senjata->status_penyimpanan === 'Personel' ? 'senjata.pembawa' : 'senjata.index';
        return redirect()->route($route)->with('success', 'Data senjata berhasil diperbarui.');
    }

    public function returnAmunisi(Request $request, $id)
    {
        $senjata = Senjata::findOrFail($id);
        
        $request->validate([
            'jumlah_kembali' => 'required|integer|min:1|max:' . ($senjata->jumlah_amunisi_dibawa ?? 0),
            'aksi' => 'required|in:kembali,pakai',
        ], [
            'jumlah_kembali.max' => 'Jumlah tidak boleh melebihi amunisi yang dibawa (' . $senjata->jumlah_amunisi_dibawa . ')',
        ]);

        $jumlah = $request->jumlah_kembali;
        $jenisAmunisi = $senjata->jenis_amunisi_dibawa;
        $aksi = $request->aksi;

        // 1. Update Senjata
        $senjata->decrement('jumlah_amunisi_dibawa', $jumlah);
        
        // Auto clear type if empty
        if ($senjata->jumlah_amunisi_dibawa <= 0) {
            $senjata->update(['jenis_amunisi_dibawa' => null]);
        }

        if ($aksi === 'kembali') {
            // 2. Return to Warehouse
            $this->returnAmunisiToGudang(
                $senjata, 
                $jenisAmunisi, 
                $jumlah, 
                'Pengembalian Amunisi'
            );
            $msg = "Berhasil mengembalikan $jumlah butir amunisi ke gudang.";
            $this->logActivity('Pengembalian Amunisi', "Mengembalikan $jumlah butir $jenisAmunisi dari senjata " . $senjata->no_senpi . " ke gudang", 'Senjata');
        } else {
            // 3. Mark as Spent (Used) - Just create history record
            \App\Models\AmunisiHistory::create([
                'satker_id' => $senjata->satker_id,
                'nama_personel' => $senjata->penanggung_jawab ?? '-',
                'pangkat_nrp' => $senjata->nrp ?? '-',
                'jenis_amunisi' => $jenisAmunisi,
                'jumlah' => -$jumlah, // Negative for used
                'tanggal' => now(),
                'keterangan' => 'Amunisi Dipakai - ' . ($request->keterangan_pakai ?? 'Tanpa keterangan') . ' (Senjata: ' . ($senjata->no_senpi ?? '-') . ')'
            ]);
            $msg = "Berhasil mencatat $jumlah butir amunisi sebagai 'Dipakai'.";
            $this->logActivity('Penggunaan Amunisi', "Mencatat $jumlah butir $jenisAmunisi dari senjata " . $senjata->no_senpi . " sebagai dipakai", 'Senjata');
        }

        return redirect()->route('senjata.pembawa')->with('success', $msg);
    }

    private function takeAmunisiFromGudang($senjata, $type, $qty, $note)
    {
        $amunisi = \App\Models\Amunisi::where('satker_id', $senjata->satker_id)
            ->where('jenis_amunisi', $type)
            ->where('status_penyimpanan', 'Gudang')
            ->first();

        if ($amunisi) {
            $amunisi->decrement('jumlah', $qty);
            
            \App\Models\AmunisiHistory::create([
                'satker_id' => $senjata->satker_id,
                'nama_personel' => $senjata->penanggung_jawab ?? '-',
                'pangkat_nrp' => $senjata->nrp ?? '-',
                'jenis_amunisi' => $type,
                'jumlah' => -$qty, // Negative for taking from warehouse
                'tanggal' => now(),
                'keterangan' => $note . ' (Senjata: ' . ($senjata->no_senpi ?? $senjata->jenis_senpi) . ')',
            ]);
        }
    }

    private function returnAmunisiToGudang($senjata, $type, $qty, $note)
    {
        $amunisi = \App\Models\Amunisi::where('satker_id', is_object($senjata) ? $senjata->satker_id : $senjata)
            ->where('jenis_amunisi', $type)
            ->where('status_penyimpanan', 'Gudang')
            ->first();

        if ($amunisi) {
            $amunisi->increment('jumlah', $qty);
        } else {
            \App\Models\Amunisi::create([
                'satker_id' => is_object($senjata) ? $senjata->satker_id : $senjata,
                'jenis_amunisi' => $type,
                'jumlah' => $qty,
                'status_penyimpanan' => 'Gudang',
                'keterangan' => 'Pengembalian dari personel',
            ]);
        }

        \App\Models\AmunisiHistory::create([
            'satker_id' => is_object($senjata) ? $senjata->satker_id : $senjata,
            'nama_personel' => is_object($senjata) ? ($senjata->penanggung_jawab ?? '-') : '-',
            'pangkat_nrp' => is_object($senjata) ? ($senjata->nrp ?? '-') : '-',
            'jenis_amunisi' => $type,
            'jumlah' => $qty,
            'tanggal' => now(),
            'keterangan' => $note . (is_object($senjata) ? ' (Senjata: ' . ($senjata->no_senpi ?? $senjata->jenis_senpi) . ')' : ''),
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids');
        if (!$ids || !is_array($ids)) {
            return response()->json(['status' => 'error', 'message' => 'Tidak ada data yang dipilih.'], 400);
        }

        $count = count($ids);
        Senjata::whereIn('id', $ids)->delete();

        $this->logActivity('Hapus Masal Senjata', "Menghapus $count data senjata secara masal.", 'Senjata');

        return response()->json(['status' => 'success', 'message' => "$count data senjata berhasil dihapus."]);
    }

    public function destroy($id)
    {
        $senjata = Senjata::findOrFail($id);

        // Return amunisi to warehouse if any
        if ($senjata->status_penyimpanan === 'Personel' && $senjata->jenis_amunisi_dibawa && $senjata->jumlah_amunisi_dibawa > 0) {
            $this->returnAmunisiToGudang($senjata, $senjata->jenis_amunisi_dibawa, $senjata->jumlah_amunisi_dibawa, 'Pengembalian (Data senjata dihapus)');
        }

        $this->logActivity('Hapus Senjata', 'Menghapus data senjata: ' . $senjata->jenis_senpi . ' (NUP: ' . ($senjata->nup ?? '-') . ')', 'Senjata');
        
        $route = $senjata->status_penyimpanan === 'Personel' ? 'senjata.pembawa' : 'senjata.index';
        $senjata->delete();

        return redirect()->route($route)->with('success', 'Data senjata berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'satker_id' => 'nullable|exists:satkers,id',
        ]);

        $satker_id = auth()->user()->satker_id ?? $request->satker_id;
        
        // Determine default status based on referrer
        $referrer = $request->headers->get('referer');
        $status_default = str_contains($referrer, 'pembawa') ? 'Personel' : 'Gudang';

        $rows = Excel::toCollection(new SenjataImport($satker_id, $status_default), $request->file('file'))->first();
        
        $conflicts = [];
        $validData = [];

        foreach ($rows as $row) {
            $jenis_senpi = $row['jenis_senpi'] ?? $row['jenis_senjata'];
            if (!$jenis_senpi || empty($jenis_senpi)) continue;

            $row_satker_id = $satker_id ?? $row['satker_id'] ?? null;
            if (!$row_satker_id) continue;

            $nup = $row['nup'] ?? null;
            $no_senpi = $row['no_senpi'] ?? null;

            // Check for existing by no_senpi or nup (only if they are not empty)
            $existing = null;
            if (!empty($no_senpi) || !empty($nup)) {
                $existing = Senjata::where(function($q) use ($no_senpi, $nup) {
                    if (!empty($no_senpi)) $q->where('no_senpi', $no_senpi);
                    if (!empty($nup)) $q->orWhere('nup', $nup);
                })->first();
            }

            if ($existing) {
                $conflicts[] = [
                    'existing' => $existing,
                    'new' => [
                        'satker_id'             => $row_satker_id,
                        'jenis_senpi'           => $jenis_senpi,
                        'laras'                 => (isset($row['laras']) && in_array($row['laras'], ['Panjang', 'Pendek'])) ? $row['laras'] : 'Panjang',
                        'nup'                   => $nup,
                        'no_senpi'              => $no_senpi,
                        'kondisi'               => (isset($row['kondisi']) && in_array($row['kondisi'], ['Baik', 'Rusak Ringan', 'Rusak Berat'])) ? $row['kondisi'] : 'Baik',
                        'status_penyimpanan'    => $row['penyimpanan'] ?? $row['status_penyimpanan'] ?? $status_default,
                        'penanggung_jawab'      => $row['nama'] ?? $row['penanggung_jawab'] ?? null,
                        'nrp'                   => $row['pangkat_nrp'] ?? $row['nrp'] ?? null,
                        'masa_berlaku_simsa'    => isset($row['masa_simsa']) ? \Carbon\Carbon::parse($row['masa_simsa']) : ($row['masa_berlaku_simsa'] ?? null),
                        'jumlah_amunisi_dibawa' => $row['jumlah_amunisi'] ?? $row['jumlah_amunisi_dibawa'] ?? 0,
                    ]
                ];
            } else {
                $validData[] = [
                    'satker_id'             => $row_satker_id,
                    'jenis_senpi'           => $jenis_senpi,
                    'laras'                 => (isset($row['laras']) && in_array($row['laras'], ['Panjang', 'Pendek'])) ? $row['laras'] : 'Panjang',
                    'nup'                   => $nup,
                    'no_senpi'              => $no_senpi,
                    'kondisi'               => (isset($row['kondisi']) && in_array($row['kondisi'], ['Baik', 'Rusak Ringan', 'Rusak Berat'])) ? $row['kondisi'] : 'Baik',
                    'status_penyimpanan'    => $row['penyimpanan'] ?? $row['status_penyimpanan'] ?? $status_default,
                    'penanggung_jawab'      => $row['nama'] ?? $row['penanggung_jawab'] ?? null,
                    'nrp'                   => $row['pangkat_nrp'] ?? $row['nrp'] ?? null,
                    'masa_berlaku_simsa'    => isset($row['masa_simsa']) ? \Carbon\Carbon::parse($row['masa_simsa']) : ($row['masa_berlaku_simsa'] ?? null),
                    'jumlah_amunisi_dibawa' => $row['jumlah_amunisi'] ?? $row['jumlah_amunisi_dibawa'] ?? 0,
                ];
            }
        }

        if (count($conflicts) > 0) {
            return response()->json([
                'status' => 'conflict',
                'conflicts' => $conflicts,
                'valid_data' => $validData
            ]);
        }

        foreach ($validData as $data) {
            Senjata::create($data);
        }

        return response()->json(['status' => 'success', 'message' => 'Data senjata berhasil diimpor.']);
    }

    public function confirmImport(Request $request)
    {
        $request->validate([
            'valid_data' => 'array',
            'resolved_conflicts' => 'array',
        ]);

        foreach ($request->valid_data as $data) {
            Senjata::create($data);
        }

        foreach ($request->resolved_conflicts as $res) {
            if ($res['decision'] == 'overwrite') {
                $existing = Senjata::find($res['existing_id']);
                if ($existing) {
                    $existing->update($res['new_data']);
                }
            }
            // if 'keep', do nothing
        }

        return response()->json(['status' => 'success', 'message' => 'Impor data berhasil diproses.']);
    }

    public function exportPdf(Request $request)
    {
        $context = $request->query('context', 'Gudang');
        $query = $this->getFilteredQuery($request, $context);
        $satker = null;

        if (auth()->user()->satker_id && !in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2', 'Pimpinan'])) {
            $satker = Satker::find(auth()->user()->satker_id);
        } elseif ($request->filled('satker_id')) {
            $satker = Satker::find($request->satker_id);
        }

        $senjatas = $query->get();
        $pdf = Pdf::loadView('senjata.pdf', compact('senjatas', 'satker', 'context'));

        if ($context === 'Personel') {
            $pdf->setPaper('a4', 'landscape');
        }

        return $pdf->download('laporan-senjata' . ($context === 'Personel' ? '-pembawa' : '') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $context = $request->query('context', 'Gudang');
        $query = $this->getFilteredQuery($request, $context);
        
        $filename = $context === 'Personel' ? 'laporan-pembawa-senjata.xlsx' : 'laporan-senjata-gudang.xlsx';
        return Excel::download(new SenjataExport($query, $context), $filename);
    }

    public function downloadTemplate(Request $request)
    {
        $context = $request->query('context', 'Gudang');
        $filename = $context === 'Personel' ? 'format-impor-pembawa-senjata.xlsx' : 'format-impor-senjata-gudang.xlsx';
        return Excel::download(new SenjataTemplateExport($context), $filename);
    }

    public function transfer(Request $request, $id)
    {
        $senjata = Senjata::findOrFail($id);
        $request->validate([
            'satker_id' => 'required|exists:satkers,id',
        ]);

        $oldSatker = $senjata->satker->nama_satker ?? 'Satker Lama';
        $newSatker = Satker::findOrFail($request->satker_id)->nama_satker;

        // Return amunisi to warehouse if any before transfer
        if ($senjata->status_penyimpanan === 'Personel' && $senjata->jenis_amunisi_dibawa && $senjata->jumlah_amunisi_dibawa > 0) {
            $this->returnAmunisiToGudang($senjata, $senjata->jenis_amunisi_dibawa, $senjata->jumlah_amunisi_dibawa, 'Pengembalian (Mutasi ke ' . $newSatker . ')');
            $senjata->update([
                'jumlah_amunisi_dibawa' => 0,
                'jenis_amunisi_dibawa' => null,
                'status_penyimpanan' => 'Gudang'
            ]);
        }

        $senjata->update(['satker_id' => $request->satker_id]);

        $this->logActivity('Mutasi Senjata', "Memindahkan senjata " . $senjata->jenis_senpi . " (NUP: " . ($senjata->nup ?? '-') . ") dari $oldSatker ke $newSatker", 'Senjata');

        return redirect()->route('senjata.index')->with('success', 'Senjata berhasil dipindahkan ke ' . $newSatker);
    }

    public function laporanRingkas(Request $request)
    {
        $satkerId = null;
        $laras = $request->input('laras');
        $kondisi = $request->input('kondisi');

        if (auth()->user()->satker_id && !in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2', 'Pimpinan'])) {
            $satkerId = auth()->user()->satker_id;
        }

        if ($request->filled('satker_id')) {
            $satkerId = $request->satker_id;
        }

        $query = Senjata::with('satker');
        if ($satkerId) {
            $query->where('satker_id', $satkerId);
        }
        if ($laras) {
            $query->where('laras', $laras);
        }
        if ($kondisi) {
            $query->where('kondisi', $kondisi);
        }

        $stats = [
            'total' => (clone $query)->count(),
            'total_baik' => (clone $query)->where('kondisi', 'Baik')->count(),
            'total_rusak_ringan' => (clone $query)->where('kondisi', 'Rusak Ringan')->count(),
            'total_rusak_berat' => (clone $query)->where('kondisi', 'Rusak Berat')->count(),
            'total_panjang' => (clone $query)->where('laras', 'Panjang')->count(),
            'total_pendek' => (clone $query)->where('laras', 'Pendek')->count(),
        ];

        $data = $query->select('satker_id', 'jenis_senpi')
            ->selectRaw("SUM(CASE WHEN kondisi = 'Baik' THEN 1 ELSE 0 END) as baik")
            ->selectRaw("SUM(CASE WHEN kondisi = 'Rusak Ringan' THEN 1 ELSE 0 END) as rusak_ringan")
            ->selectRaw("SUM(CASE WHEN kondisi = 'Rusak Berat' THEN 1 ELSE 0 END) as rusak_berat")
            ->selectRaw("COUNT(*) as jumlah")
            ->groupBy('satker_id', 'jenis_senpi')
            ->get()
            ->map(function($item) {
                return [
                    'satker' => $item->satker ? $item->satker->nama_satker : 'Unknown',
                    'jenis_senpi' => $item->jenis_senpi,
                    'baik' => (int)$item->baik,
                    'rusak_ringan' => (int)$item->rusak_ringan,
                    'rusak_berat' => (int)$item->rusak_berat,
                    'jumlah' => (int)$item->jumlah,
                ];
            })
            ->sortBy('satker')
            ->values();

        $satkers = Satker::all();

        return view('senjata.laporan-ringkas', compact('data', 'stats', 'satkers', 'satkerId', 'laras', 'kondisi'));
    }
}

