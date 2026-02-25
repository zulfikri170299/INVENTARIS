<?php

namespace App\Http\Controllers;

use App\Models\Alsus;
use App\Models\Satker;
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
    public function index(Request $request)
    {
        $query = Alsus::with('satker');

        if (auth()->user()->satker_id && !in_array(auth()->user()->role, ['Super Admin'])) {
            $query->where('satker_id', auth()->user()->satker_id);
        }

        if ($request->filled('search')) {
            $query->where('jenis_barang', 'like', '%' . $request->search . '%')
                  ->orWhere('nup', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('satker_id')) {
            $query->where('satker_id', $request->satker_id);
        }

        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }

        $perPage = $request->input('per_page', 10);
        $alsuses = $query->latest()->paginate($perPage)->withQueryString();
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

        if (auth()->user()->satker_id && !in_array(auth()->user()->role, ['Super Admin'])) {
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

        if (auth()->user()->satker_id && !in_array(auth()->user()->role, ['Super Admin'])) {
            $validated['satker_id'] = auth()->user()->satker_id;
        }

        \Log::debug("Updating Alsus ID: " . $id, $validated);
        $alsus->update($validated);

        $this->logActivity('Update Alsus', 'Memperbarui data alsus: ' . $alsus->jenis_barang . ' (NUP: ' . ($alsus->nup ?? '-') . ')', 'Alsus');

        return redirect()->route('alsus.index')->with('success', 'Data alsus berhasil diperbarui.');
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

            $existing = Alsus::where('nup', $row['nup'])->first();

            if ($existing) {
                $conflicts[] = [
                    'existing' => $existing,
                    'new' => [
                        'satker_id' => $satker_id,
                        'jenis_barang' => $row['jenis_barang'],
                        'nup' => $row['nup'],
                        'kondisi' => $row['kondisi'] ?? 'Baik',
                        'keterangan' => $row['keterangan'] ?? null,
                    ]
                ];
            } else {
                $validData[] = [
                    'satker_id' => $satker_id,
                    'jenis_barang' => $row['jenis_barang'],
                    'nup' => $row['nup'],
                    'kondisi' => $row['kondisi'] ?? 'Baik',
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
        $query = Alsus::with('satker');
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

        $alsuses = $query->get();
        $pdf = Pdf::loadView('alsus.pdf', compact('alsuses', 'satker'));

        return $pdf->download('laporan-alsus.pdf');
    }

    public function exportExcel(Request $request)
    {
        $query = Alsus::with('satker');

        if (auth()->user()->satker_id && !in_array(auth()->user()->role, ['Super Admin'])) {
            $query->where('satker_id', auth()->user()->satker_id);
        }

        if ($request->filled('search')) {
            $query->where('jenis_barang', 'like', '%' . $request->search . '%')
                  ->orWhere('nup', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('satker_id')) {
            $query->where('satker_id', $request->satker_id);
        }

        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }

        return Excel::download(new AlsusExport($query), 'laporan-alsus.xlsx');
    }

    public function downloadTemplate()
    {
        return Excel::download(new AlsusTemplateExport, 'format-impor-alsus.xlsx');
    }
}
