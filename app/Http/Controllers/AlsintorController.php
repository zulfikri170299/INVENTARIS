<?php

namespace App\Http\Controllers;

use App\Models\Alsintor;
use App\Models\Satker;
use App\Imports\AlsintorImport;
use App\Exports\AlsintorTemplateExport;
use App\Exports\AlsintorExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\LogActivity;

class AlsintorController extends Controller
{
    use LogActivity;
    public function index(Request $request)
    {
        $query = Alsintor::with('satker');

        if (auth()->user()->satker_id && !in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2', 'Pimpinan'])) {
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
        $alsintors = $query->latest()->paginate($perPage)->withQueryString();
        $satkers = Satker::all();

        return view('alsintor.index', compact('alsintors', 'satkers'));
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

        $alsintor = Alsintor::create($validated);

        $this->logActivity('Tambah Alsintor', 'Menambahkan data alsintor: ' . $alsintor->jenis_barang . ' (NUP: ' . ($alsintor->nup ?? '-') . ')', 'Alsintor');

        return back()->with('success', 'Data alsintor berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $alsintor = Alsintor::findOrFail($id);

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

        \Log::debug("Updating Alsintor ID: " . $id, $validated);
        $alsintor->update($validated);

        $this->logActivity('Update Alsintor', 'Memperbarui data alsintor: ' . $alsintor->jenis_barang . ' (NUP: ' . ($alsintor->nup ?? '-') . ')', 'Alsintor');

        return redirect()->route('alsintor.index')->with('success', 'Data alsintor berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $alsintor = Alsintor::findOrFail($id);
        $this->logActivity('Hapus Alsintor', 'Menghapus data alsintor: ' . $alsintor->jenis_barang . ' (NUP: ' . ($alsintor->nup ?? '-') . ')', 'Alsintor');
        $alsintor->delete();
        return redirect()->route('alsintor.index')->with('success', 'Data alsintor berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'satker_id' => 'nullable|exists:satkers,id',
        ]);

        $satker_id = auth()->user()->satker_id ?? $request->satker_id;
        $rows = Excel::toCollection(new AlsintorImport($satker_id), $request->file('file'))->first();
        
        $conflicts = [];
        $validData = [];

        foreach ($rows as $row) {
            if (!isset($row['jenis_barang']) || empty($row['jenis_barang'])) continue;

            $existing = Alsintor::where('nup', $row['nup'])->first();

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
            Alsintor::create($data);
        }

        return response()->json(['status' => 'success', 'message' => 'Data Alsintor berhasil diimpor.']);
    }

    public function confirmImport(Request $request)
    {
        $request->validate([
            'valid_data' => 'array',
            'resolved_conflicts' => 'array',
        ]);

        foreach ($request->valid_data as $data) {
            Alsintor::create($data);
        }

        foreach ($request->resolved_conflicts as $res) {
            if ($res['decision'] == 'overwrite') {
                $existing = Alsintor::find($res['existing_id']);
                if ($existing) {
                    $existing->update($res['new_data']);
                }
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Impor data berhasil diproses.']);
    }

    public function exportPdf(Request $request)
    {
        $query = Alsintor::with('satker');
        $satker = null;

        if (auth()->user()->satker_id && !in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2', 'Pimpinan'])) {
            $satkerId = auth()->user()->satker_id;
            $query->where('satker_id', $satkerId);
            $satker = Satker::find($satkerId);
        } elseif ($request->filled('satker_id')) {
            $satkerId = $request->satker_id;
            $query->where('satker_id', $satkerId);
            $satker = Satker::find($satkerId);
        }

        $alsintors = $query->get();
        $pdf = Pdf::loadView('alsintor.pdf', compact('alsintors', 'satker'));

        return $pdf->download('laporan-alsintor.pdf');
    }

    public function exportExcel(Request $request)
    {
        $query = \App\Models\Alsintor::with('satker');

        if (auth()->user()->satker_id && !in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2', 'Pimpinan'])) {
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

        return Excel::download(new AlsintorExport($query), 'laporan-alsintor.xlsx');
    }

    public function downloadTemplate()
    {
        return Excel::download(new AlsintorTemplateExport, 'format-impor-alsintor.xlsx');
    }

    public function transfer(Request $request, $id)
    {
        $alsintor = Alsintor::findOrFail($id);
        $request->validate([
            'satker_id' => 'required|exists:satkers,id',
        ]);

        $oldSatker = $alsintor->satker->nama_satker ?? 'Satker Lama';
        $newSatker = Satker::findOrFail($request->satker_id)->nama_satker;

        $alsintor->update(['satker_id' => $request->satker_id]);

        $this->logActivity('Mutasi Alsintor', "Memindahkan alsintor " . $alsintor->jenis_barang . " (NUP: " . ($alsintor->nup ?? '-') . ") dari $oldSatker ke $newSatker", 'Alsintor');

        return redirect()->route('alsintor.index')->with('success', 'Data alsintor berhasil dipindahkan ke ' . $newSatker);
    }
}
