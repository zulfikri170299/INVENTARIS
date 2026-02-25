<?php

namespace App\Http\Controllers;

use App\Models\AmunisiHistory;
use App\Models\Satker;
use Illuminate\Http\Request;

class AmunisiHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = AmunisiHistory::with('satker');

        if (auth()->user()->satker_id && !in_array(auth()->user()->role, ['Super Admin'])) {
            $query->where('satker_id', auth()->user()->satker_id);
        }

        if ($request->filled('satker_id')) {
            $query->where('satker_id', $request->satker_id);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_personel', 'like', '%' . $request->search . '%')
                  ->orWhere('pangkat_nrp', 'like', '%' . $request->search . '%')
                  ->orWhere('jenis_amunisi', 'like', '%' . $request->search . '%');
            });
        }

        $perPage = $request->input('per_page', 10);
        $histories = $query->latest()->paginate($perPage)->withQueryString();
        $satkers = Satker::all();

        return view('amunisi_history.index', compact('histories', 'satkers'));
    }
}
