<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AmunisiTemplateExport implements WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'NO',
            'JENIS AMUNISI',
            'JUMLAH',
            'STATUS PENYIMPANAN',
            'KETERANGAN',
        ];
    }
}
