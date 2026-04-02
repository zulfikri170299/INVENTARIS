<?php

namespace App\Http\Controllers;

use App\Models\Alsus;
use App\Models\Alsintor;
use App\Models\Satker;
use App\Exports\AlsusAlsintorExport;
use App\Imports\AlsusImport;
use App\Exports\AlsusTemplateExport;
use App\Exports\AlsusExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\LogActivity;

class AlsusController extends Controller
{
    use LogActivity;
    private function getFilteredQuery(Request $request)
    {
        $query = Alsus::with('satker');

        if (auth()->user()->satker_id && !in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2', 'Pimpinan'])) {
            $query->where('satker_id', auth()->user()->satker_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('jenis_barang', 'like', '%' . $search . '%')
                  ->orWhere('nup', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('satker_id')) {
            $query->where('satker_id', $request->satker_id);
        }

        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }

        return $query->latest();
    }

    public function index(Request $request)
    {
        $query = $this->getFilteredQuery($request);
        $perPage = $request->input('per_page', 10);
        $alsuses = $query->paginate($perPage)->withQueryString();
        $satkers = Satker::all();

        return view('alsus.index', compact('alsuses', 'satkers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'satker_id' => 'required|exists:satkers,id',
            'jenis_barang' => 'required|string',
            'nup' => 'nullable|string',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'keterangan' => 'nullable|string',
        ]);

        if (auth()->user()->satker_id && !in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2', 'Pimpinan'])) {
            $validated['satker_id'] = auth()->user()->satker_id;
        }

        $alsus = Alsus::create($validated);

        $this->logActivity('Tambah Alsus', 'Menambahkan data alsus: ' . $alsus->jenis_barang . ' (NUP: ' . ($alsus->nup ?? '-') . ')', 'Alsus');

        return back()->with('success', 'Data alsus berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $alsus = Alsus::findOrFail($id);

        $validated = $request->validate([
            'satker_id' => 'required|exists:satkers,id',
            'jenis_barang' => 'required|string',
            'nup' => 'nullable|string',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'keterangan' => 'nullable|string',
        ]);

        if (auth()->user()->satker_id && !in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2', 'Pimpinan'])) {
            $validated['satker_id'] = auth()->user()->satker_id;
        }

        \Log::debug("Updating Alsus ID: " . $id, $validated);
        $alsus->update($validated);

        $this->logActivity('Update Alsus', 'Memperbarui data alsus: ' . $alsus->jenis_barang . ' (NUP: ' . ($alsus->nup ?? '-') . ')', 'Alsus');

        return redirect()->route('alsus.index')->with('success', 'Data alsus berhasil diperbarui.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids');
        if (!$ids || !is_array($ids)) {
            return response()->json(['status' => 'error', 'message' => 'Tidak ada data yang dipilih.'], 400);
        }

        $count = count($ids);
        Alsus::whereIn('id', $ids)->delete();

        $this->logActivity('Hapus Masal Alsus', "Menghapus $count data alsus secara masal.", 'Alsus');

        return response()->json(['status' => 'success', 'message' => "$count data alsus berhasil dihapus."]);
    }

    public function destroy($id)
    {
        $alsus = Alsus::findOrFail($id);
        $this->logActivity('Hapus Alsus', 'Menghapus data alsus: ' . $alsus->jenis_barang . ' (NUP: ' . ($alsus->nup ?? '-') . ')', 'Alsus');
        $alsus->delete();
        return redirect()->route('alsus.index')->with('success', 'Data alsus berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'satker_id' => 'nullable|exists:satkers,id',
        ]);

        $satker_id = auth()->user()->satker_id ?? $request->satker_id;
        $rows = Excel::toCollection(new AlsusImport($satker_id), $request->file('file'))->first();
        
        $conflicts = [];
        $validData = [];

        foreach ($rows as $row) {
            if (!isset($row['jenis_barang']) || empty($row['jenis_barang'])) continue;

            $row_satker_id = $satker_id ?? $row['satker_id'] ?? null;
            if (!$row_satker_id) continue;

            $nup = $row['nup'] ?? null;
            $existing = !empty($nup) ? Alsus::where('nup', $nup)->first() : null;

            if ($existing) {
                $conflicts[] = [
                    'existing' => $existing,
                    'new' => [
                        'satker_id' => $row_satker_id,
                        'jenis_barang' => $row['jenis_barang'],
                        'nup' => $nup,
                        'kondisi' => (isset($row['kondisi']) && in_array($row['kondisi'], ['Baik', 'Rusak Ringan', 'Rusak Berat'])) ? $row['kondisi'] : 'Baik',
                        'keterangan' => $row['keterangan'] ?? null,
                    ]
                ];
            } else {
                $validData[] = [
                    'satker_id' => $row_satker_id,
                    'jenis_barang' => $row['jenis_barang'],
                    'nup' => $nup,
                    'kondisi' => (isset($row['kondisi']) && in_array($row['kondisi'], ['Baik', 'Rusak Ringan', 'Rusak Berat'])) ? $row['kondisi'] : 'Baik',
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
            Alsus::create($data);
        }

        return response()->json(['status' => 'success', 'message' => 'Data Alsus berhasil diimpor.']);
    }

    public function confirmImport(Request $request)
    {
        $request->validate([
            'valid_data' => 'array',
            'resolved_conflicts' => 'array',
        ]);

        foreach ($request->valid_data as $data) {
            Alsus::create($data);
        }

        foreach ($request->resolved_conflicts as $res) {
            if ($res['decision'] == 'overwrite') {
                $existing = Alsus::find($res['existing_id']);
                if ($existing) {
                    $existing->update($res['new_data']);
                }
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Impor data berhasil diproses.']);
    }

    public function exportPdf(Request $request)
    {
        $query = $this->getFilteredQuery($request);
        $satker = null;

        if (auth()->user()->satker_id && !in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2', 'Pimpinan'])) {
            $satker = Satker::find(auth()->user()->satker_id);
        } elseif ($request->filled('satker_id')) {
            $satker = Satker::find($request->satker_id);
        }

        $alsuses = $query->get();
        $pdf = Pdf::loadView('alsus.pdf', compact('alsuses', 'satker'));

        return $pdf->download('laporan-alsus.pdf');
    }

    public function exportExcel(Request $request)
    {
        $query = $this->getFilteredQuery($request);
        return Excel::download(new AlsusExport($query), 'laporan-alsus.xlsx');
    }

    public function downloadTemplate()
    {
        return Excel::download(new AlsusTemplateExport, 'format-impor-alsus.xlsx');
    }

    public function transfer(Request $request, $id)
    {
        $alsus = Alsus::findOrFail($id);
        $request->validate([
            'satker_id' => 'required|exists:satkers,id',
        ]);

        $oldSatker = $alsus->satker->nama_satker ?? 'Satker Lama';
        $newSatker = Satker::findOrFail($request->satker_id)->nama_satker;

        $alsus->update(['satker_id' => $request->satker_id]);

        $this->logActivity('Mutasi Alsus', "Memindahkan alsus " . $alsus->jenis_barang . " (NUP: " . ($alsus->nup ?? '-') . ") dari $oldSatker ke $newSatker", 'Alsus');

        return redirect()->route('alsus.index')->with('success', 'Data alsus berhasil dipindahkan ke ' . $newSatker);
    }

    public function laporanRingkas(Request $request)
    {
        $satkerId = null;
        $tipe = $request->input('tipe', 'semua');
        $kondisi = $request->input('kondisi');

        if (auth()->user()->satker_id && !in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2'])) {
            $satkerId = auth()->user()->satker_id;
        }

        if ($request->filled('satker_id')) {
            $satkerId = $request->satker_id;
        }

        // Get Alsus data
        $dataAlsus = collect();
        $totalAlsus = 0;
        $alsusBaik = 0; $alsusRR = 0; $alsusRB = 0;
        
        if ($tipe === 'semua' || $tipe === 'alsus') {
            $baseQuery = Alsus::query();
            if ($satkerId) $baseQuery->where('satker_id', $satkerId);
            if ($kondisi) $baseQuery->where('kondisi', $kondisi);

            $totalAlsus = (clone $baseQuery)->count();
            $alsusBaik = (clone $baseQuery)->where('kondisi', 'Baik')->count();
            $alsusRR = (clone $baseQuery)->where('kondisi', 'Rusak Ringan')->count();
            $alsusRB = (clone $baseQuery)->where('kondisi', 'Rusak Berat')->count();

            $dataAlsus = $baseQuery->select('satker_id', 'jenis_barang')
                ->selectRaw("SUM(CASE WHEN kondisi = 'Baik' THEN 1 ELSE 0 END) as baik")
                ->selectRaw("SUM(CASE WHEN kondisi = 'Rusak Ringan' THEN 1 ELSE 0 END) as rusak_ringan")
                ->selectRaw("SUM(CASE WHEN kondisi = 'Rusak Berat' THEN 1 ELSE 0 END) as rusak_berat")
                ->selectRaw("COUNT(*) as jumlah")
                ->groupBy('satker_id', 'jenis_barang')
                ->with('satker')
                ->get();
        }

        // Get Alsintor data
        $dataAlsintor = collect();
        $totalAlsintor = 0;
        $alsintorBaik = 0; $alsintorRR = 0; $alsintorRB = 0;

        if ($tipe === 'semua' || $tipe === 'alsintor') {
            $baseQuery = Alsintor::query();
            if ($satkerId) $baseQuery->where('satker_id', $satkerId);
            if ($kondisi) $baseQuery->where('kondisi', $kondisi);

            $totalAlsintor = (clone $baseQuery)->count();
            $alsintorBaik = (clone $baseQuery)->where('kondisi', 'Baik')->count();
            $alsintorRR = (clone $baseQuery)->where('kondisi', 'Rusak Ringan')->count();
            $alsintorRB = (clone $baseQuery)->where('kondisi', 'Rusak Berat')->count();

            $dataAlsintor = $baseQuery->select('satker_id', 'jenis_barang')
                ->selectRaw("SUM(CASE WHEN kondisi = 'Baik' THEN 1 ELSE 0 END) as baik")
                ->selectRaw("SUM(CASE WHEN kondisi = 'Rusak Ringan' THEN 1 ELSE 0 END) as rusak_ringan")
                ->selectRaw("SUM(CASE WHEN kondisi = 'Rusak Berat' THEN 1 ELSE 0 END) as rusak_berat")
                ->selectRaw("COUNT(*) as jumlah")
                ->groupBy('satker_id', 'jenis_barang')
                ->with('satker')
                ->get();
        }

        $data = $dataAlsus->concat($dataAlsintor)->map(function($item) {
            return [
                'satker' => $item->satker ? $item->satker->nama_satker : 'Unknown',
                'jenis_barang' => $item->jenis_barang,
                'baik' => (int)$item->baik,
                'rusak_ringan' => (int)$item->rusak_ringan,
                'rusak_berat' => (int)$item->rusak_berat,
                'jumlah' => (int)$item->jumlah,
            ];
        })->sortBy('satker')->values();

        // Statistics
        $stats = [
            'total_alsus' => $totalAlsus,
            'total_alsintor' => $totalAlsintor,
            'total_baik' => $alsusBaik + $alsintorBaik,
            'total_rusak_ringan' => $alsusRR + $alsintorRR,
            'total_rusak_berat' => $alsusRB + $alsintorRB,
            'total' => $totalAlsus + $totalAlsintor,
        ];

        $satkers = Satker::all();

        return view('alsus.laporan-ringkas', compact('data', 'stats', 'satkers', 'satkerId', 'tipe', 'kondisi'));
    }

    public function exportSummary(Request $request)
    {
        $satkerId = null;

        if (auth()->user()->satker_id && !in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2'])) {
            $satkerId = auth()->user()->satker_id;
        }

        if ($request->filled('satker_id')) {
            $satkerId = $request->satker_id;
        }

        return Excel::download(new AlsusAlsintorExport($satkerId), 'laporan-ringkas-alsus-alsintor.xlsx');
    }
}
