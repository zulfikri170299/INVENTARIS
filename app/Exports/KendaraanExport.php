<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class KendaraanExport implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize
{
    protected $query;
    protected $no = 0;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'NO',
            'SATKER',
            'JENIS KENDARAAN',
            'PLAT NOMOR',
            'NO RANGKA',
            'NO MESIN',
            'NUP',
            'TAHUN PEMBUATAN',
            'RODA',
            'KONDISI',
            'BAHAN BAKAR',
            'PENANGGUNG JAWAB',
            'NRP',
            'KETERANGAN',
        ];
    }

    public function map($kendaraan): array
    {
        $this->no++;
        return [
            $this->no,
            $kendaraan->satker->nama_satker ?? '-',
            $kendaraan->jenis_kendaraan,
            $kendaraan->nopol,
            $kendaraan->no_rangka,
            $kendaraan->no_mesin,
            $kendaraan->nup,
            $kendaraan->tahun_pembuatan,
            $kendaraan->jenis_roda,
            $kendaraan->kondisi,
            $kendaraan->bahan_bakar,
            $kendaraan->penanggung_jawab,
            $kendaraan->nrp,
            $kendaraan->keterangan,
        ];
    }
}
