<?php

namespace App\Http\Controllers;

use App\Models\Satker;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Traits\LogActivity;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\SatkerExport;

class SatkerController extends Controller implements HasMiddleware
{
    use LogActivity;
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                if (!in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2'])) {
                    abort(403, 'Akses ditolak.');
                }
                return $next($request);
            }),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    private function getFilteredQuery(Request $request)
    {
        $query = Satker::withCount(['senjatas', 'kendaraans', 'alsuses', 'alsintors', 'amunisis']);

        if ($request->filled('search')) {
            $query->where('nama_satker', 'like', '%' . $request->search . '%');
        }

        return $query->latest();
    }

    public function index(Request $request)
    {
        $query = $this->getFilteredQuery($request);
        $perPage = $request->input('per_page', 10);
        $satkers = $query->paginate($perPage)->withQueryString();

        return view('satker.index', compact('satkers'));
    }

    public function exportExcel(Request $request)
    {
        $query = $this->getFilteredQuery($request);
        return Excel::download(new SatkerExport($query), 'data-satker.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $satkers = $this->getFilteredQuery($request)->get();
        $pdf = Pdf::loadView('satker.pdf', compact('satkers'));
        return $pdf->download('data-satker.pdf');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_satker' => 'required|string|max:255|unique:satkers,nama_satker',
        ]);

        $satker = Satker::create($validated);

        // Create automatic user for Satker
        $this->createAutomaticUser($satker);

        $this->logActivity('Tambah Satker', 'Menambahkan satker baru dan membuat akun admin: ' . $satker->nama_satker, 'Manajemen Satker');

        return back()->with('success', 'Satuan Kerja dan Akun Admin berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $satker = Satker::findOrFail($id);
        
        $validated = $request->validate([
            'nama_satker' => 'required|string|max:255|unique:satkers,nama_satker,' . $satker->id,
        ]);

        \Log::debug("Updating Satker ID: " . $id, $validated);
        $satker->update($validated);

        $this->logActivity('Update Satker', 'Memperbarui data satker: ' . $satker->nama_satker, 'Manajemen Satker');

        return redirect()->route('satker.index')->with('success', 'Satuan Kerja berhasil diperbarui.');
    }

    public function destroy(Satker $satker)
    {
        // Check for inventory dependencies
        $kendaraanCount = $satker->kendaraans()->count();
        $senjataCount = $satker->senjatas()->count();
        $alsusCount = $satker->alsuses()->count();
        $alsintorCount = $satker->alsintors()->count();
        $amunisiCount = $satker->amunisis()->count();
        $userCount = $satker->users()->count();

        $inventoryTotal = $kendaraanCount + $senjataCount + $alsusCount + $alsintorCount + $amunisiCount;

        if ($inventoryTotal > 0) {
            $details = [];
            if ($kendaraanCount > 0) $details[] = "$kendaraanCount Kendaraan";
            if ($senjataCount > 0) $details[] = "$senjataCount Senjata";
            if ($alsusCount > 0) $details[] = "$alsusCount Alsus";
            if ($alsintorCount > 0) $details[] = "$alsintorCount Alsintor";
            if ($amunisiCount > 0) $details[] = "$amunisiCount Amunisi";

            return back()->with('error', 'Maaf Data Inventaris Satker Tersebut Masih Terdaftar Pada Aplikasi, Silahkan di Hapus Dulu Data Invetarisnya.');
        }

        // If no inventory, we can delete the satker and its users
        $this->logActivity('Hapus Satker', 'Menghapus satker: ' . $satker->nama_satker . ' (Termasuk ' . $userCount . ' user terkait)', 'Manajemen Satker');
        
        // Delete associated users first
        $satker->users()->delete();
        $satker->delete();

        return back()->with('success', 'Satuan Kerja dan seluruh akun terkait berhasil dihapus.');
    }

    public function downloadTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\SatkerTemplateExport, 'template_import_satker.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        $file = $request->file('file');
        $import = new \App\Imports\SatkerImport;
        $rows = \Maatwebsite\Excel\Facades\Excel::toArray($import, $file)[0];

        $conflicts = [];
        $newItems = [];

        foreach ($rows as $row) {
            if (empty($row['nama_satker'])) continue;

            $existing = Satker::where('nama_satker', $row['nama_satker'])->first();

            if ($existing) {
                $conflicts[] = [
                    'existing' => $existing,
                    'new' => $row
                ];
            } else {
                $newItems[] = $row;
            }
        }

        if (!empty($conflicts)) {
            return back()->with([
                'import_conflicts' => $conflicts,
                'pending_import' => $newItems
            ]);
        }

        foreach ($newItems as $item) {
            $satker = Satker::create([
                'nama_satker' => $item['nama_satker']
            ]);
            
            // Create automatic user for Satker
            $this->createAutomaticUser($satker);
        }

        return back()->with('success', count($newItems) . ' data Satker dan Akun Admin berhasil diimpor.');
    }

    public function confirmImport(Request $request)
    {
        $action = $request->input('action');
        $conflicts = json_decode($request->input('conflicts'), true);
        $pending = json_decode($request->input('pending'), true);

        $importedCount = 0;

        if ($action === 'skip') {
            foreach ($pending as $item) {
                $satker = Satker::create($item);
                $this->createAutomaticUser($satker);
                $importedCount++;
            }
        } elseif ($action === 'update' || $action === 'all') {
            foreach ($pending as $item) {
                $satker = Satker::create($item);
                $this->createAutomaticUser($satker);
                $importedCount++;
            }
            foreach ($conflicts as $conflict) {
                $satker = Satker::find($conflict['existing']['id']);
                if ($satker) {
                    $satker->update($conflict['new']);
                    $this->createAutomaticUser($satker);
                    $importedCount++;
                }
            }
        }

        return redirect()->route('satker.index')->with('success', $importedCount . ' data Satker dan Akun Admin berhasil diproses.');
    }

    private function createAutomaticUser($satker)
    {
        $credentials = strtolower(str_replace(' ', '', $satker->nama_satker)) . '@invenlog.com';
        
        // Try to find user by satker_id or email
        $user = User::where('satker_id', $satker->id)
                    ->orWhere('email', $credentials)
                    ->first();

        if ($user) {
            \Log::info("Updating existing user for Satker: " . $satker->nama_satker);
            $user->update([
                'name' => 'Admin ' . $satker->nama_satker,
                'email' => $credentials,
                'password' => $credentials, // Model handles hashing
                'role' => 'Admin Satker',
                'satker_id' => $satker->id,
            ]);
        } else {
            \Log::info("Creating new user for Satker: " . $satker->nama_satker);
            User::create([
                'name' => 'Admin ' . $satker->nama_satker,
                'email' => $credentials,
                'password' => $credentials, // Model handles hashing
                'role' => 'Admin Satker',
                'satker_id' => $satker->id,
            ]);
        }
    }
}
