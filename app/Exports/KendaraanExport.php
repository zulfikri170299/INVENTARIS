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
            'No',
            'Satker',
            'Jenis Kendaraan',
            'Plat Nomor',
            'No Rangka',
            'No Mesin',
            'NUP',
            'Tahun Pembuatan',
            'Roda',
            'Kondisi',
            'Bahan Bakar',
            'Penanggung Jawab',
            'NRP',
            'Keterangan',
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
