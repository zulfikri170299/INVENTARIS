<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SenjataExport implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize
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
            'Jenis Senpi',
            'Laras',
            'NUP',
            'No Senpi',
            'Kondisi',
            'Status Penyimpanan',
            'Penanggung Jawab',
            'NRP',
            'Masa Berlaku SIMSA',
            'Keterangan',
        ];
    }

    public function map($senjata): array
    {
        $this->no++;
        return [
            $this->no,
            $senjata->satker->nama_satker ?? '-',
            $senjata->jenis_senpi,
            $senjata->laras,
            $senjata->nup,
            $senjata->no_senpi,
            $senjata->kondisi,
            $senjata->status_penyimpanan,
            $senjata->penanggung_jawab,
            $senjata->nrp,
            $senjata->masa_berlaku_simsa,
            $senjata->keterangan,
        ];
    }
}
