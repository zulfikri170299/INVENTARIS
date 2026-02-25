<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AlsintorExport implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize
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
            'Jenis Barang',
            'NUP',
            'Kondisi',
            'Keterangan',
        ];
    }

    public function map($alsintor): array
    {
        $this->no++;
        return [
            $this->no,
            $alsintor->satker->nama_satker ?? '-',
            $alsintor->jenis_barang,
            $alsintor->nup,
            $alsintor->kondisi,
            $alsintor->keterangan,
        ];
    }
}
