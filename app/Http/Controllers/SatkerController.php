<?php

namespace App\Http\Controllers;

use App\Models\Satker;
use Illuminate\Http\Request;

class SatkerController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2'])) {
                abort(403, 'Akses ditolak.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $satkers = Satker::latest()->paginate(10);
        return view('satker.index', compact('satkers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_satker' => 'required|string|max:255|unique:satkers,nama_satker',
        ]);

        Satker::create($validated);

        return back()->with('success', 'Satuan Kerja berhasil ditambahkan.');
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

        return redirect()->route('satker.index')->with('success', 'Satuan Kerja berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Satker $satker)
    {
        // Optional: Check if Satker is used in other tables before deleting
        if ($satker->users()->count() > 0 || $satker->senjatas()->count() > 0) {
            return back()->with('error', 'Satker tidak bisa dihapus karena masih digunakan oleh data lain.');
        }

        $satker->delete();

        return back()->with('success', 'Satuan Kerja berhasil dihapus.');
    }
}
