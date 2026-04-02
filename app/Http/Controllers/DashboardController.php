<?php

namespace App\Http\Controllers;

use App\Models\Senjata;
use App\Models\Kendaraan;
use App\Models\Alsus;
use App\Models\Alsintor;
use App\Models\Amunisi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $satkerId = $user->satker_id;

        // Base queries
        $senjataQuery = Senjata::with('satker');
        $kendaraanQuery = Kendaraan::query();
        $alsusQuery = Alsus::query();
        $alsintorQuery = Alsintor::query();
        $amunisiQuery = Amunisi::query();

        // Data Isolation
        if ($satkerId && !in_array($user->role, ['Super Admin', 'Super Admin 2', 'Pimpinan'])) {
            $senjataQuery->where('satker_id', $satkerId);
            $kendaraanQuery->where('satker_id', $satkerId);
            $alsusQuery->where('satker_id', $satkerId);
            $alsintorQuery->where('satker_id', $satkerId);
            $amunisiQuery->where('satker_id', $satkerId);
        }

        // Totals
        $totalSenjata = $senjataQuery->count();
        $totalKendaraan = $kendaraanQuery->count();
        $totalAlsus = $alsusQuery->count();
        $totalAlsintor = $alsintorQuery->count();
        $totalAmunisi = $amunisiQuery->sum('jumlah');

        // Kendaraan by Roda
        $kendaraanR2 = (clone $kendaraanQuery)->where('jenis_roda', 'R2')->count();
        $kendaraanR4 = (clone $kendaraanQuery)->where('jenis_roda', 'R4')->count();
        $kendaraanR6 = (clone $kendaraanQuery)->where('jenis_roda', 'R6')->count();
        $kendaraanR8 = (clone $kendaraanQuery)->where('jenis_roda', 'R8')->count();

        // Senjata by Laras
        $senjataPanjang = (clone $senjataQuery)->where('laras', 'Panjang')->count();
        $senjataPendek = (clone $senjataQuery)->where('laras', 'Pendek')->count();

        // Senjata by Status
        $senjataGudang = (clone $senjataQuery)->where('status_penyimpanan', 'Gudang')->count();
        $senjataPersonel = (clone $senjataQuery)->where('status_penyimpanan', 'Personel')->count();

        // SIMSA Monitoring
        $now = Carbon::now();
        $thirtyDaysLater = Carbon::now()->addDays(30);

        // SIMSA Stats for Chart
        $simsaBaseQuery = (clone $senjataQuery)->whereNotNull('masa_berlaku_simsa');
        $simsaExpiredCount = (clone $simsaBaseQuery)->where('masa_berlaku_simsa', '<=', $now->toDateString())->count();
        $simsaNearExpiryCount = (clone $simsaBaseQuery)
            ->where('masa_berlaku_simsa', '>', $now->toDateString())
            ->where('masa_berlaku_simsa', '<=', $thirtyDaysLater->toDateString())
            ->count();
        $simsaSafeCount = (clone $simsaBaseQuery)->where('masa_berlaku_simsa', '>', $thirtyDaysLater->toDateString())->count();
        $totalSimsa = $simsaBaseQuery->count();

        return view('dashboard', compact(
            'totalSenjata', 'totalKendaraan', 'totalAlsus', 'totalAlsintor', 'totalAmunisi',
            'kendaraanR2', 'kendaraanR4', 'kendaraanR6', 'kendaraanR8',
            'senjataPanjang', 'senjataPendek', 'senjataGudang', 'senjataPersonel',
            'simsaExpiredCount', 'simsaNearExpiryCount', 'simsaSafeCount', 'totalSimsa'
        ));
    }
}
