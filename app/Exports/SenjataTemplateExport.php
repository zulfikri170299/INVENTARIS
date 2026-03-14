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
        $headings = [
            'SATKER ID',
            'JENIS SENPI',
            'LARAS',
            'NUP',
            'NO SENPI',
            'KONDISI',
            'KETERANGAN',
        ];

        if ($this->context === 'Personel') {
            array_splice($headings, 6, 0, [
                'PENANGGUNG JAWAB',
                'PANGKAT NRP',
                'MASA BERLAKU SIMSA',
                'JENIS AMUNISI DIBAWA',
                'JUMLAH AMUNISI DIBAWA'
            ]);
        }

        return $headings;
    }
}
