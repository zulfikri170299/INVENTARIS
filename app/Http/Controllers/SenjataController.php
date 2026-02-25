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
    public function index(Request $request)
    {
        $query = Senjata::with('satker');

        if (auth()->user()->satker_id && !in_array(auth()->user()->role, ['Super Admin'])) {
            $query->where('satker_id', auth()->user()->satker_id);
        }

        if ($request->filled('search')) {
            $query->where('jenis_senpi', 'like', '%' . $request->search . '%')
                  ->orWhere('nup', 'like', '%' . $request->search . '%')
                  ->orWhere('penanggung_jawab', 'like', '%' . $request->search . '%');
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

        if ($request->filled('status_penyimpanan')) {
            $query->where('status_penyimpanan', $request->status_penyimpanan);
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

        $perPage = $request->input('per_page', 10);
        $senjatas = $query->latest()->paginate($perPage)->withQueryString();
        $satkers = Satker::all();

        // Ambil jenis amunisi beserta total stoknya sesuai satker yang sedang login
        $amunisiQuery = \App\Models\Amunisi::select('jenis_amunisi')
            ->selectRaw('SUM(jumlah) as total_stok')
            ->groupBy('jenis_amunisi');

        if (auth()->user()->satker_id && !in_array(auth()->user()->role, ['Super Admin'])) {
            $amunisiQuery->where('satker_id', auth()->user()->satker_id);
        }
        $availableAmunisi = $amunisiQuery->orderBy('jenis_amunisi')->get();

        return view('senjata.index', compact('senjatas', 'satkers', 'availableAmunisi'));
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

        if (auth()->user()->satker_id && !in_array(auth()->user()->role, ['Super Admin'])) {
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
                    'jumlah' => $senjata->jumlah_amunisi_dibawa,
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

        if (auth()->user()->satker_id && !in_array(auth()->user()->role, ['Super Admin'])) {
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

        return redirect()->route('senjata.index')->with('success', 'Data senjata berhasil diperbarui.');
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
                'jumlah' => $jumlah,
                'tanggal' => now(),
                'keterangan' => 'Amunisi Dipakai - ' . ($request->keterangan_pakai ?? 'Tanpa keterangan') . ' (Senjata: ' . ($senjata->no_senpi ?? '-') . ')'
            ]);
            $msg = "Berhasil mencatat $jumlah butir amunisi sebagai 'Dipakai'.";
            $this->logActivity('Penggunaan Amunisi', "Mencatat $jumlah butir $jenisAmunisi dari senjata " . $senjata->no_senpi . " sebagai dipakai", 'Senjata');
        }

        return redirect()->route('senjata.index')->with('success', $msg);
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
                'jumlah' => $qty,
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

    public function destroy($id)
    {
        $senjata = Senjata::findOrFail($id);

        // Return amunisi to warehouse if any
        if ($senjata->status_penyimpanan === 'Personel' && $senjata->jenis_amunisi_dibawa && $senjata->jumlah_amunisi_dibawa > 0) {
            $this->returnAmunisiToGudang($senjata, $senjata->jenis_amunisi_dibawa, $senjata->jumlah_amunisi_dibawa, 'Pengembalian (Data senjata dihapus)');
        }

        $this->logActivity('Hapus Senjata', 'Menghapus data senjata: ' . $senjata->jenis_senpi . ' (NUP: ' . ($senjata->nup ?? '-') . ')', 'Senjata');
        $senjata->delete();
        return redirect()->route('senjata.index')->with('success', 'Data senjata berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'satker_id' => 'nullable|exists:satkers,id',
        ]);

        $satker_id = auth()->user()->satker_id ?? $request->satker_id;
        $rows = Excel::toCollection(new SenjataImport($satker_id), $request->file('file'))->first();
        
        $conflicts = [];
        $validData = [];

        foreach ($rows as $row) {
            if (!isset($row['jenis_senpi']) || empty($row['jenis_senpi'])) continue;

            // Check for existing by no_senpi or nup
            $existing = Senjata::where('no_senpi', $row['no_senpi'])
                ->orWhere('nup', $row['nup'])
                ->first();

            if ($existing) {
                $conflicts[] = [
                    'existing' => $existing,
                    'new' => [
                        'satker_id' => $satker_id,
                        'jenis_senpi' => $row['jenis_senpi'],
                        'laras' => $row['laras'],
                        'nup' => $row['nup'],
                        'no_senpi' => $row['no_senpi'],
                        'kondisi' => $row['kondisi'] ?? 'Baik',
                        'penanggung_jawab' => $row['penanggung_jawab'],
                        'nrp' => $row['pangkat_nrp'] ?? $row['nrp'] ?? null,
                        'status_penyimpanan' => $row['status_penyimpanan'],
                        'masa_berlaku_simsa' => $row['masa_berlaku_simsa'] ?? null,
                        'keterangan' => $row['keterangan'] ?? null,
                    ]
                ];
            } else {
                $validData[] = [
                    'satker_id' => $satker_id,
                    'jenis_senpi' => $row['jenis_senpi'],
                    'laras' => $row['laras'],
                    'nup' => $row['nup'],
                    'no_senpi' => $row['no_senpi'],
                    'kondisi' => $row['kondisi'] ?? 'Baik',
                    'penanggung_jawab' => $row['penanggung_jawab'],
                    'nrp' => $row['pangkat_nrp'] ?? $row['nrp'] ?? null,
                    'status_penyimpanan' => $row['status_penyimpanan'],
                    'masa_berlaku_simsa' => $row['masa_berlaku_simsa'] ?? null,
                    'keterangan' => $row['keterangan'] ?? null,
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
        $query = Senjata::with('satker');
        $satker = null;

        if (auth()->user()->satker_id) {
            $satkerId = auth()->user()->satker_id;
            $query->where('satker_id', $satkerId);
            $satker = Satker::find($satkerId);
        } elseif ($request->filled('satker_id')) {
            $satkerId = $request->satker_id;
            $query->where('satker_id', $satkerId);
            $satker = Satker::find($satkerId);
        }

        $senjatas = $query->get();
        $pdf = Pdf::loadView('senjata.pdf', compact('senjatas', 'satker'));

        return $pdf->download('laporan-senjata.pdf');
    }

    public function exportExcel(Request $request)
    {
        $query = Senjata::with('satker');

        if (auth()->user()->satker_id && !in_array(auth()->user()->role, ['Super Admin'])) {
            $query->where('satker_id', auth()->user()->satker_id);
        }

        if ($request->filled('search')) {
            $query->where('jenis_senpi', 'like', '%' . $request->search . '%')
                  ->orWhere('nup', 'like', '%' . $request->search . '%')
                  ->orWhere('penanggung_jawab', 'like', '%' . $request->search . '%');
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

        if ($request->filled('status_penyimpanan')) {
            $query->where('status_penyimpanan', $request->status_penyimpanan);
        }

        return Excel::download(new SenjataExport($query), 'laporan-senjata.xlsx');
    }

    public function downloadTemplate()
    {
        return Excel::download(new SenjataTemplateExport, 'format-impor-senjata.xlsx');
    }
}
