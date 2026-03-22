<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\Satker;
use App\Imports\KendaraanImport;
use App\Exports\KendaraanTemplateExport;
use App\Exports\KendaraanExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\LogActivity;

class KendaraanController extends Controller
{
    use LogActivity;
    private function getFilteredQuery(Request $request)
    {
        $query = Kendaraan::with('satker');

        if (auth()->user()->satker_id && !in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2', 'Pimpinan'])) {
            $query->where('satker_id', auth()->user()->satker_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('jenis_kendaraan', 'like', '%' . $search . '%')
                  ->orWhere('nup', 'like', '%' . $search . '%')
                  ->orWhere('nopol', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('satker_id')) {
            $query->where('satker_id', $request->satker_id);
        }

        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }

        if ($request->filled('jenis_roda')) {
            $query->where('jenis_roda', $request->jenis_roda);
        }

        return $query->latest();
    }

    public function index(Request $request)
    {
        $query = $this->getFilteredQuery($request);
        $perPage = $request->input('per_page', 10);
        $kendaraans = $query->paginate($perPage)->withQueryString();
        $satkers = Satker::all();

        return view('kendaraan.index', compact('kendaraans', 'satkers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'satker_id' => 'required|exists:satkers,id',
            'jenis_roda' => 'required|in:R2,R4,R6,R8',
            'jenis_kendaraan' => 'required|string',
            'nup' => 'nullable|string',
            'tahun_pembuatan' => 'nullable|string',
            'no_mesin' => 'nullable|string',
            'no_rangka' => 'nullable|string',
            'nopol' => 'nullable|string',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'bahan_bakar' => 'required|in:Pertalite,Pertamax,Pertamina Dex,Listrik',
            'penanggung_jawab' => 'nullable|string',
            'nrp' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        if (auth()->user()->satker_id && !in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2', 'Pimpinan'])) {
            $validated['satker_id'] = auth()->user()->satker_id;
        }

        $kendaraan = Kendaraan::create($validated);

        $this->logActivity('Tambah Kendaraan', 'Menambahkan kendaraan baru: ' . $kendaraan->jenis_kendaraan . ' (Nopol: ' . ($kendaraan->nopol ?? '-') . ')', 'Kendaraan');

        return back()->with('success', 'Data kendaraan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $kendaraan = Kendaraan::findOrFail($id);

        $validated = $request->validate([
            'satker_id' => 'required|exists:satkers,id',
            'jenis_roda' => 'required|in:R2,R4,R6,R8',
            'jenis_kendaraan' => 'required|string',
            'nup' => 'nullable|string',
            'no_rangka' => 'nullable|string',
            'nopol' => 'nullable|string',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'bahan_bakar' => 'required|in:Pertalite,Pertamax,Pertamina Dex,Listrik',
            'penanggung_jawab' => 'nullable|string',
            'nrp' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        if (auth()->user()->satker_id && !in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2', 'Pimpinan'])) {
            $validated['satker_id'] = auth()->user()->satker_id;
        }

        \Log::debug("Updating Kendaraan ID: " . $id, $validated);
        $kendaraan->update($validated);

        $this->logActivity('Update Kendaraan', 'Memperbarui data kendaraan: ' . $kendaraan->jenis_kendaraan . ' (Nopol: ' . ($kendaraan->nopol ?? '-') . ')', 'Kendaraan');

        return redirect()->route('kendaraan.index')->with('success', 'Data kendaraan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kendaraan = Kendaraan::findOrFail($id);
        $this->logActivity('Hapus Kendaraan', 'Menghapus data kendaraan: ' . $kendaraan->jenis_kendaraan . ' (Nopol: ' . ($kendaraan->nopol ?? '-') . ')', 'Kendaraan');
        $kendaraan->delete();
        return redirect()->route('kendaraan.index')->with('success', 'Data kendaraan berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'satker_id' => 'nullable|exists:satkers,id',
        ]);

        $satker_id = auth()->user()->satker_id ?? $request->satker_id;
        $rows = Excel::toCollection(new KendaraanImport($satker_id), $request->file('file'))->first();
        
        $conflicts = [];
        $validData = [];

        foreach ($rows as $row) {
            if (!isset($row['jenis_kendaraan']) || empty($row['jenis_kendaraan'])) continue;

            $row_satker_id = $satker_id ?? $row['satker_id'] ?? null;
            if (!$row_satker_id) continue;

            $nopol = $row['nopol'] ?? null;
            $no_rangka = $row['no_rangka'] ?? null;

            // Check for existing by nopol or no_rangka (only if they are not empty)
            $existing = null;
            if (!empty($nopol) || !empty($no_rangka)) {
                $existing = Kendaraan::where(function($q) use ($nopol, $no_rangka) {
                    if (!empty($nopol)) $q->where('nopol', $nopol);
                    if (!empty($no_rangka)) $q->orWhere('no_rangka', $no_rangka);
                })->first();
            }

            if ($existing) {
                $conflicts[] = [
                    'existing' => $existing,
                    'new' => [
                        'satker_id' => $row_satker_id,
                        'jenis_roda' => (isset($row['jenis_roda']) && in_array($row['jenis_roda'], ['R2', 'R4', 'R6'])) ? $row['jenis_roda'] : 'R4',
                        'jenis_kendaraan' => $row['jenis_kendaraan'],
                        'nup' => $row['nup'] ?? null,
                        'no_rangka' => $no_rangka,
                        'nopol' => $nopol,
                        'kondisi' => (isset($row['kondisi']) && in_array($row['kondisi'], ['Baik', 'Rusak Ringan', 'Rusak Berat'])) ? $row['kondisi'] : 'Baik',
                        'bahan_bakar' => $row['bahan_bakar'] ?? '-',
                        'penanggung_jawab' => $row['penanggung_jawab'] ?? '-',
                        'nrp' => $row['pangkat_nrp'] ?? $row['nrp'] ?? null,
                        'keterangan' => $row['keterangan'] ?? null,
                    ]
                ];
            } else {
                $validData[] = [
                    'satker_id' => $row_satker_id,
                    'jenis_roda' => (isset($row['jenis_roda']) && in_array($row['jenis_roda'], ['R2', 'R4', 'R6'])) ? $row['jenis_roda'] : 'R4',
                    'jenis_kendaraan' => $row['jenis_kendaraan'],
                    'nup' => $row['nup'] ?? null,
                    'no_rangka' => $no_rangka,
                    'nopol' => $nopol,
                    'kondisi' => (isset($row['kondisi']) && in_array($row['kondisi'], ['Baik', 'Rusak Ringan', 'Rusak Berat'])) ? $row['kondisi'] : 'Baik',
                    'bahan_bakar' => $row['bahan_bakar'] ?? '-',
                    'penanggung_jawab' => $row['penanggung_jawab'] ?? '-',
                    'nrp' => $row['pangkat_nrp'] ?? $row['nrp'] ?? null,
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
            Kendaraan::create($data);
        }

        return response()->json(['status' => 'success', 'message' => 'Data kendaraan berhasil diimpor.']);
    }

    public function confirmImport(Request $request)
    {
        $request->validate([
            'valid_data' => 'array',
            'resolved_conflicts' => 'array',
        ]);

        foreach ($request->valid_data as $data) {
            Kendaraan::create($data);
        }

        foreach ($request->resolved_conflicts as $res) {
            if ($res['decision'] == 'overwrite') {
                $existing = Kendaraan::find($res['existing_id']);
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

        $kendaraans = $query->get();
        $pdf = Pdf::loadView('kendaraan.pdf', compact('kendaraans', 'satker'));

        return $pdf->download('laporan-kendaraan.pdf');
    }

    public function exportExcel(Request $request)
    {
        $query = $this->getFilteredQuery($request);
        return Excel::download(new KendaraanExport($query), 'laporan-kendaraan.xlsx');
    }

    public function downloadTemplate()
    {
        return Excel::download(new KendaraanTemplateExport, 'format-impor-kendaraan.xlsx');
    }

    public function transfer(Request $request, $id)
    {
        $kendaraan = Kendaraan::findOrFail($id);
        $request->validate([
            'satker_id' => 'required|exists:satkers,id',
        ]);

        $oldSatker = $kendaraan->satker->nama_satker ?? 'Satker Lama';
        $newSatker = Satker::findOrFail($request->satker_id)->nama_satker;

        $kendaraan->update(['satker_id' => $request->satker_id]);

        $this->logActivity('Mutasi Kendaraan', "Memindahkan kendaraan " . $kendaraan->jenis_kendaraan . " (Nopol: " . ($kendaraan->nopol ?? '-') . ") dari $oldSatker ke $newSatker", 'Kendaraan');

        return redirect()->route('kendaraan.index')->with('success', 'Data kendaraan berhasil dipindahkan ke ' . $newSatker);
    }

    public function laporanRingkas(Request $request)
    {
        $satkerId = null;
        $jenisRoda = $request->input('jenis_roda');
        $kondisi = $request->input('kondisi');

        if (auth()->user()->satker_id && !in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2'])) {
            $satkerId = auth()->user()->satker_id;
        }

        if ($request->filled('satker_id')) {
            $satkerId = $request->satker_id;
        }

        $query = Kendaraan::with('satker');
        if ($satkerId) {
            $query->where('satker_id', $satkerId);
        }
        if ($jenisRoda) {
            $query->where('jenis_roda', $jenisRoda);
        }
        if ($kondisi) {
            $query->where('kondisi', $kondisi);
        }

        $stats = [
            'total' => (clone $query)->count(),
            'total_baik' => (clone $query)->where('kondisi', 'Baik')->count(),
            'total_rusak_ringan' => (clone $query)->where('kondisi', 'Rusak Ringan')->count(),
            'total_rusak_berat' => (clone $query)->where('kondisi', 'Rusak Berat')->count(),
            'total_r2' => (clone $query)->where('jenis_roda', 'R2')->count(),
            'total_r4' => (clone $query)->where('jenis_roda', 'R4')->count(),
            'total_r6' => (clone $query)->where('jenis_roda', 'R6')->count(),
            'total_r8' => (clone $query)->where('jenis_roda', 'R8')->count(),
        ];

        $data = $query->select('satker_id', 'jenis_roda')
            ->selectRaw("SUM(CASE WHEN kondisi = 'Baik' THEN 1 ELSE 0 END) as baik")
            ->selectRaw("SUM(CASE WHEN kondisi = 'Rusak Ringan' THEN 1 ELSE 0 END) as rusak_ringan")
            ->selectRaw("SUM(CASE WHEN kondisi = 'Rusak Berat' THEN 1 ELSE 0 END) as rusak_berat")
            ->selectRaw("COUNT(*) as jumlah")
            ->groupBy('satker_id', 'jenis_roda')
            ->get()
            ->map(function($item) {
                return [
                    'satker' => $item->satker ? $item->satker->nama_satker : 'Unknown',
                    'jenis_roda' => $item->jenis_roda,
                    'baik' => (int)$item->baik,
                    'rusak_ringan' => (int)$item->rusak_ringan,
                    'rusak_berat' => (int)$item->rusak_berat,
                    'jumlah' => (int)$item->jumlah,
                ];
            })
            ->sortBy('satker')
            ->values();

        $satkers = Satker::all();

        return view('kendaraan.laporan-ringkas', compact('data', 'stats', 'satkers', 'satkerId', 'jenisRoda', 'kondisi'));
    }
}
