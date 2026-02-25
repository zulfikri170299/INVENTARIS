<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->role !== 'Super Admin') {
            abort(403, 'Hanya Super Admin yang dapat mengakses log aktivitas.');
        }

        $query = ActivityLog::with('user');

        if ($request->filled('search')) {
            $query->where('activity', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
        }

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        $perPage = $request->input('per_page', 20);
        $logs = $query->latest()->paginate($perPage)->withQueryString();

        $modules = ActivityLog::distinct()->pluck('module');

        return view('activity-log.index', compact('logs', 'modules'));
    }
}
