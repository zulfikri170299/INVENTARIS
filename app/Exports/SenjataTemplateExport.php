<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SenjataTemplateExport implements WithHeadings, ShouldAutoSize
{
    protected $context;

    public function __construct($context = 'Gudang')
    {
        $this->context = $context;
    }

    public function headings(): array
    {
        if ($this->context === 'Personel') {
            return [
                'NO',
                'SATKER ID',
                'JENIS SENPI',
                'LARAS',
                'NUP',
                'NO SENPI',
                'KONDISI',
                'JUMLAH AMUNISI',
                'NAMA',
                'PANGKAT/NRP',
                'MASA SIMSA',
                'KETERANGAN'
            ];
        }

        return [
            'NO',
            'SATKER ID',
            'JENIS SENPI',
            'LARAS',
            'NUP',
            'NO SENPI',
            'KONDISI',
        ];
    }
}
