<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\ActivityLogExport;

class ActivityLogController extends Controller
{
    use \App\Traits\LogActivity;

    private function getFilteredQuery(Request $request)
    {
        $query = ActivityLog::with('user');

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('activity', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        return $query->latest();
    }

    public function index(Request $request)
    {
        if (auth()->user()->role !== 'Super Admin' && auth()->user()->role !== 'Super Admin 2') {
            abort(403, 'Hanya Super Admin yang dapat mengakses log aktivitas.');
        }

        $query = $this->getFilteredQuery($request);
        $perPage = $request->input('per_page', 20);
        $logs = $query->paginate($perPage)->withQueryString();

        $modules = ActivityLog::distinct()->pluck('module');

        return view('activity-log.index', compact('logs', 'modules'));
    }

    public function exportExcel(Request $request)
    {
        $query = $this->getFilteredQuery($request);
        return Excel::download(new ActivityLogExport($query), 'log-aktivitas.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $logs = $this->getFilteredQuery($request)->get();
        $pdf = Pdf::loadView('activity-log.pdf', compact('logs'))->setPaper('a4', 'landscape');
        return $pdf->download('log-aktivitas.pdf');
    }
}
