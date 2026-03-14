<?php

namespace App\Http\Controllers;

use App\Models\Senjata;
use App\Models\Kendaraan;
use App\Models\Alsus;
use App\Models\Alsintor;
use App\Models\Amunisi;
use App\Models\PengajuanBerkas;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $satkerId = $user->satker_id;
        $isSuperAdmin = in_array($user->role, ['Super Admin', 'Super Admin 2']);

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

        // Recent Pengajuan Berkas (for Stats)
        $pengajuanQuery = PengajuanBerkas::query();
        if ($satkerId && !$isSuperAdmin) {
            $pengajuanQuery->where('satker_id', $satkerId);
        }

        // Penghapusan Stats
        $penghapusanQuery = (clone $pengajuanQuery)->where('kategori', 'penghapusan');
        $penghapusanCounts = [
            'diajukan' => (clone $penghapusanQuery)->where('status', 'diajukan')->count(),
            'diterima' => (clone $penghapusanQuery)->where('status', 'diterima')->count(),
            'diproses' => (clone $penghapusanQuery)->where('status', 'diproses')->count(),
            'dikembalikan' => (clone $penghapusanQuery)->where('status', 'dikembalikan')->count(),
            'naik_ke_kapolda' => (clone $penghapusanQuery)->where('status', 'naik_ke_kapolda')->count(),
            'ditandatangani' => (clone $penghapusanQuery)->where('status', 'ditandatangani')->count(),
            'selesai' => (clone $penghapusanQuery)->where('status', 'selesai')->count(),
        ];
        $totalPenghapusan = array_sum($penghapusanCounts);

        // Penetapan Status Stats
        $penetapanQuery = (clone $pengajuanQuery)->where('kategori', 'penetapan_status');
        $penetapanCounts = [
            'diajukan' => (clone $penetapanQuery)->where('status', 'diajukan')->count(),
            'diterima' => (clone $penetapanQuery)->where('status', 'diterima')->count(),
            'diproses' => (clone $penetapanQuery)->where('status', 'diproses')->count(),
            'dikembalikan' => (clone $penetapanQuery)->where('status', 'dikembalikan')->count(),
            'naik_ke_kapolda' => (clone $penetapanQuery)->where('status', 'naik_ke_kapolda')->count(),
            'ditandatangani' => (clone $penetapanQuery)->where('status', 'ditandatangani')->count(),
            'selesai' => (clone $penetapanQuery)->where('status', 'selesai')->count(),
        ];
        $totalPenetapan = array_sum($penetapanCounts);

        return view('dashboard', compact(
            'totalSenjata', 'totalKendaraan', 'totalAlsus', 'totalAlsintor', 'totalAmunisi',
            'kendaraanR2', 'kendaraanR4', 'kendaraanR6', 'kendaraanR8',
            'senjataPanjang', 'senjataPendek', 'senjataGudang', 'senjataPersonel',
            'penghapusanCounts', 'totalPenghapusan', 'penetapanCounts', 'totalPenetapan',
            'simsaExpiredCount', 'simsaNearExpiryCount', 'simsaSafeCount', 'totalSimsa'
        ));
    }
}
