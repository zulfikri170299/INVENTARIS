<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AmunisiExport implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize
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
            'JENIS AMUNISI',
            'JUMLAH',
            'STATUS PENYIMPANAN',
            'KETERANGAN',
        ];
    }

    public function map($amunisi): array
    {
        $this->no++;
        return [
            $this->no,
            $amunisi->satker->nama_satker ?? '-',
            $amunisi->jenis_amunisi,
            $amunisi->jumlah,
            $amunisi->status_penyimpanan,
            $amunisi->keterangan,
        ];
    }
}
