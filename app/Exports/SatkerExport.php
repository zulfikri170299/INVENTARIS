<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SatkerExport implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize
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
            'NAMA SATUAN KERJA',
            'TOTAL SENJATA',
            'TOTAL KENDARAAN',
            'TOTAL ALSINTOR',
            'TOTAL ALSUS',
            'TOTAL AMUNISI',
            'TOTAL INVENTARIS',
        ];
    }

    public function map($satker): array
    {
        $this->no++;
        $senjata = $satker->senjatas_count ?? 0;
        $kendaraan = $satker->kendaraans_count ?? 0;
        $alsintor = $satker->alsintors_count ?? 0;
        $alsus = $satker->alsuses_count ?? 0;
        $amunisi = $satker->amunisis_count ?? 0;
        $total = $senjata + $kendaraan + $alsintor + $alsus + $amunisi;

        return [
            $this->no,
            $satker->nama_satker,
            $senjata,
            $kendaraan,
            $alsintor,
            $alsus,
            $amunisi,
            $total,
        ];
    }
}
