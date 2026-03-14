<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AmunisiTemplateExport implements WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'JENIS AMUNISI',
            'JUMLAH',
            'STATUS PENYIMPANAN',
            'KETERANGAN',
        ];
    }
}
