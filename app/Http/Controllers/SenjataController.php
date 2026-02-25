<?php

namespace App\Http\Controllers;

use App\Models\Senjata;
use App\Models\Satker;
use App\Imports\SenjataImport;
use App\Exports\SenjataTemplateExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class SenjataController extends Controller
{
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

        $senjatas = $query->latest()->paginate(10);
        $satkers = Satker::all();

        return view('senjata.index', compact('senjatas', 'satkers'));
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
            'keterangan' => 'nullable|string',
        ]);

        if (auth()->user()->satker_id && !in_array(auth()->user()->role, ['Super Admin'])) {
            $validated['satker_id'] = auth()->user()->satker_id;
        }

        Senjata::create($validated);

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
            'keterangan' => 'nullable|string',
        ]);

        if (auth()->user()->satker_id && !in_array(auth()->user()->role, ['Super Admin'])) {
            $validated['satker_id'] = auth()->user()->satker_id;
        }

        \Log::debug("Updating Senjata ID: " . $id, $validated);
        $senjata->update($validated);

        return redirect()->route('senjata.index')->with('success', 'Data senjata berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $senjata = Senjata::findOrFail($id);
        \Log::debug("Menghapus Senjata ID: " . $senjata->id);
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

    public function downloadTemplate()
    {
        return Excel::download(new SenjataTemplateExport, 'format-impor-senjata.xlsx');
    }
}
