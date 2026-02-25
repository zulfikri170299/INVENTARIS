<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\Satker;
use App\Imports\KendaraanImport;
use App\Exports\KendaraanTemplateExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class KendaraanController extends Controller
{
    public function index(Request $request)
    {
        $query = Kendaraan::with('satker');

        if (auth()->user()->satker_id && !in_array(auth()->user()->role, ['Super Admin'])) {
            $query->where('satker_id', auth()->user()->satker_id);
        }

        if ($request->filled('search')) {
            $query->where('jenis_kendaraan', 'like', '%' . $request->search . '%')
                  ->orWhere('nup', 'like', '%' . $request->search . '%')
                  ->orWhere('nopol', 'like', '%' . $request->search . '%');
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

        $kendaraans = $query->latest()->paginate(10);
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
            'no_rangka' => 'nullable|string',
            'nopol' => 'nullable|string',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'bahan_bakar' => 'required|in:Pertalite,Pertamax,Pertamina Dex,Listrik',
            'penanggung_jawab' => 'nullable|string',
            'nrp' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        if (auth()->user()->satker_id && !in_array(auth()->user()->role, ['Super Admin'])) {
            $validated['satker_id'] = auth()->user()->satker_id;
        }

        Kendaraan::create($validated);

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

        if (auth()->user()->satker_id && !in_array(auth()->user()->role, ['Super Admin'])) {
            $validated['satker_id'] = auth()->user()->satker_id;
        }

        \Log::debug("Updating Kendaraan ID: " . $id, $validated);
        $kendaraan->update($validated);

        return redirect()->route('kendaraan.index')->with('success', 'Data kendaraan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kendaraan = Kendaraan::findOrFail($id);
        \Log::debug("Menghapus Kendaraan ID: " . $kendaraan->id);
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

            // Check for existing by nopol or no_rangka
            $existing = Kendaraan::where('nopol', $row['nopol'])
                ->orWhere('no_rangka', $row['no_rangka'])
                ->first();

            if ($existing) {
                $conflicts[] = [
                    'existing' => $existing,
                    'new' => [
                        'satker_id' => $satker_id,
                        'jenis_roda' => $row['jenis_roda'] ?? 'R4',
                        'jenis_kendaraan' => $row['jenis_kendaraan'],
                        'nup' => $row['nup'],
                        'no_rangka' => $row['no_rangka'],
                        'nopol' => $row['nopol'],
                        'kondisi' => $row['kondisi'] ?? 'Baik',
                        'bahan_bakar' => $row['bahan_bakar'],
                        'penanggung_jawab' => $row['penanggung_jawab'],
                        'nrp' => $row['pangkat_nrp'] ?? $row['nrp'] ?? null,
                        'keterangan' => $row['keterangan'] ?? null,
                    ]
                ];
            } else {
                $validData[] = [
                    'satker_id' => $satker_id,
                    'jenis_roda' => $row['jenis_roda'] ?? 'R4',
                    'jenis_kendaraan' => $row['jenis_kendaraan'],
                    'nup' => $row['nup'],
                    'no_rangka' => $row['no_rangka'],
                    'nopol' => $row['nopol'],
                    'kondisi' => $row['kondisi'] ?? 'Baik',
                    'bahan_bakar' => $row['bahan_bakar'],
                    'penanggung_jawab' => $row['penanggung_jawab'],
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
        $query = Kendaraan::with('satker');
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

        $kendaraans = $query->get();
        $pdf = Pdf::loadView('kendaraan.pdf', compact('kendaraans', 'satker'));

        return $pdf->download('laporan-kendaraan.pdf');
    }

    public function downloadTemplate()
    {
        return Excel::download(new KendaraanTemplateExport, 'format-impor-kendaraan.xlsx');
    }
}
