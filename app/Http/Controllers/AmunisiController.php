<?php

namespace App\Http\Controllers;

use App\Models\Amunisi;
use App\Models\Satker;
use App\Models\AmunisiHistory;
use App\Imports\AmunisiImport;
use App\Exports\AmunisiTemplateExport;
use App\Exports\AmunisiExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\LogActivity;

class AmunisiController extends Controller
{
    use LogActivity;

    private function getFilteredQuery(Request $request)
    {
        $query = Amunisi::with('satker');

        if (auth()->user()->satker_id && !in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2', 'Pimpinan'])) {
            $query->where('satker_id', auth()->user()->satker_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('jenis_amunisi', 'like', '%' . $search . '%')
                  ->orWhere('keterangan', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('satker_id')) {
            $query->where('satker_id', $request->satker_id);
        }

        // Default to warehouse stock if not specified
        $status = $request->input('status_penyimpanan', 'Gudang');
        $query->where('status_penyimpanan', $status);

        return $query->latest();
    }

    public function index(Request $request)
    {
        $query = $this->getFilteredQuery($request);
        $perPage = $request->input('per_page', 10);
        $amunisis = $query->paginate($perPage)->withQueryString();
        $satkers = Satker::all();
        $jenis_amunisi_list = Amunisi::distinct()->pluck('jenis_amunisi');

        return view('amunisi.index', compact('amunisis', 'satkers', 'jenis_amunisi_list'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'satker_id' => 'required|exists:satkers,id',
            'jenis_amunisi' => 'required|string',
            'jumlah' => 'required|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $validated['status_penyimpanan'] = 'Gudang';

        if (auth()->user()->satker_id && !in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2', 'Pimpinan'])) {
            $validated['satker_id'] = auth()->user()->satker_id;
        }

        $amunisi = Amunisi::create($validated);

        $this->logActivity('Tambah Amunisi', 'Menambahkan amunisi baru: ' . $amunisi->jenis_amunisi . ' (' . $amunisi->jumlah . ')', 'Amunisi');

        return redirect()->route('amunisi.index')->with('success', 'Data amunisi berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $amunisi = Amunisi::findOrFail($id);
        
        $validated = $request->validate([
            'satker_id' => 'required|exists:satkers,id',
            'jenis_amunisi' => 'required|string',
            'jumlah' => 'required|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $validated['status_penyimpanan'] = 'Gudang';

        if (auth()->user()->satker_id && !in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2', 'Pimpinan'])) {
            $validated['satker_id'] = auth()->user()->satker_id;
        }

        $amunisi->update($validated);

        $this->logActivity('Update Amunisi', 'Memperbarui data amunisi: ' . $amunisi->jenis_amunisi . ' (' . $amunisi->jumlah . ')', 'Amunisi');

        return redirect()->route('amunisi.index')->with('success', 'Data amunisi berhasil diperbarui.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids');
        if (!$ids || !is_array($ids)) {
            return response()->json(['status' => 'error', 'message' => 'Tidak ada data yang dipilih.'], 400);
        }

        $count = count($ids);
        Amunisi::whereIn('id', $ids)->delete();

        $this->logActivity('Hapus Masal Amunisi', "Menghapus $count data amunisi secara masal.", 'Amunisi');

        return response()->json(['status' => 'success', 'message' => "$count data amunisi berhasil dihapus."]);
    }

    public function destroy($id)
    {
        $amunisi = Amunisi::findOrFail($id);
        $this->logActivity('Hapus Amunisi', 'Menghapus data amunisi: ' . $amunisi->jenis_amunisi . ' (' . $amunisi->jumlah . ')', 'Amunisi');
        $amunisi->delete();
        return redirect()->route('amunisi.index')->with('success', 'Data amunisi berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'satker_id' => 'nullable|exists:satkers,id',
        ]);

        $satker_id = auth()->user()->satker_id ?? $request->satker_id;
        $rows = Excel::toCollection(new AmunisiImport($satker_id), $request->file('file'))->first();
        
        $conflicts = [];
        $validData = [];

        foreach ($rows as $row) {
            if (!isset($row['jenis_amunisi']) || empty($row['jenis_amunisi'])) continue;

            $row_satker_id = $satker_id ?? $row['satker_id'] ?? null;
            if (!$row_satker_id) continue;

            // Check for existing by jenis_amunisi and status_penyimpanan in the same satker
            $existing = Amunisi::where('satker_id', $row_satker_id)
                ->where('jenis_amunisi', $row['jenis_amunisi'])
                ->where('status_penyimpanan', $row['status_penyimpanan'] ?? 'Gudang')
                ->first();

            if ($existing) {
                $conflicts[] = [
                    'existing' => $existing,
                    'new' => [
                        'satker_id' => $row_satker_id,
                        'jenis_amunisi' => $row['jenis_amunisi'],
                        'jumlah' => $row['jumlah'] ?? 0,
                        'status_penyimpanan' => $row['status_penyimpanan'] ?? 'Gudang',
                        'keterangan' => $row['keterangan'] ?? null,
                    ]
                ];
            } else {
                $validData[] = [
                    'satker_id' => $row_satker_id,
                    'jenis_amunisi' => $row['jenis_amunisi'],
                    'jumlah' => $row['jumlah'] ?? 0,
                    'status_penyimpanan' => $row['status_penyimpanan'] ?? 'Gudang',
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
            Amunisi::create($data);
        }

        return response()->json(['status' => 'success', 'message' => 'Data amunisi berhasil diimpor.']);
    }

    public function confirmImport(Request $request)
    {
        $request->validate([
            'valid_data' => 'array',
            'resolved_conflicts' => 'array',
        ]);

        foreach ($request->valid_data as $data) {
            Amunisi::create($data);
        }

        foreach ($request->resolved_conflicts as $res) {
            if ($res['decision'] == 'overwrite') {
                $existing = Amunisi::find($res['existing_id']);
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

        $amunisis = $query->get();
        $pdf = Pdf::loadView('amunisi.pdf', compact('amunisis', 'satker'));

        return $pdf->download('laporan-amunisi.pdf');
    }

    public function exportExcel(Request $request)
    {
        $query = $this->getFilteredQuery($request);
        return Excel::download(new AmunisiExport($query), 'laporan-amunisi.xlsx');
    }

    public function downloadTemplate()
    {
        return Excel::download(new AmunisiTemplateExport, 'format-impor-amunisi.xlsx');
    }

    public function transfer(Request $request, $id)
    {
        $amunisi = Amunisi::findOrFail($id);
        $request->validate([
            'satker_id' => 'required|exists:satkers,id',
            'jumlah_transfer' => 'required|integer|min:1|max:' . $amunisi->jumlah,
        ]);

        $oldSatkerId = $amunisi->satker_id;
        $oldSatkerNama = $amunisi->satker->nama_satker ?? 'Satker Lama';
        $newSatkerId = $request->satker_id;
        $newSatkerNama = \App\Models\Satker::findOrFail($newSatkerId)->nama_satker;
        $jumlah = $request->jumlah_transfer;

        // Deduct from current amunisi
        $amunisi->decrement('jumlah', $jumlah);

        // Record History for Sender (OUT)
        AmunisiHistory::create([
            'satker_id' => $oldSatkerId,
            'nama_personel' => '-',
            'pangkat_nrp' => '-',
            'jenis_amunisi' => $amunisi->jenis_amunisi,
            'jumlah' => -$jumlah, // Negative for out
            'tanggal' => now(),
            'keterangan' => 'Dikirim ke ' . $newSatkerNama,
        ]);

        // Add to new amunisi (or create)
        $targetAmunisi = Amunisi::where('satker_id', $newSatkerId)
            ->where('jenis_amunisi', $amunisi->jenis_amunisi)
            ->where('status_penyimpanan', 'Gudang')
            ->first();

        if ($targetAmunisi) {
            $targetAmunisi->increment('jumlah', $jumlah);
        } else {
            Amunisi::create([
                'satker_id' => $newSatkerId,
                'jenis_amunisi' => $amunisi->jenis_amunisi,
                'jumlah' => $jumlah,
                'status_penyimpanan' => 'Gudang',
                'keterangan' => 'Mutasi dari ' . $oldSatkerNama,
            ]);
        }

        // Record History for Receiver (IN)
        AmunisiHistory::create([
            'satker_id' => $newSatkerId,
            'nama_personel' => '-',
            'pangkat_nrp' => '-',
            'jenis_amunisi' => $amunisi->jenis_amunisi,
            'jumlah' => $jumlah, // Positive for in
            'tanggal' => now(),
            'keterangan' => 'Diterima dari ' . $oldSatkerNama,
        ]);

        $this->logActivity('Mutasi Amunisi', "Memindahkan $jumlah butir amunisi " . $amunisi->jenis_amunisi . " dari $oldSatkerNama ke $newSatkerNama", 'Amunisi');

        return redirect()->route('amunisi.index')->with('success', "Berhasil memindahkan $jumlah butir amunisi ke $newSatkerNama");
    }
}
