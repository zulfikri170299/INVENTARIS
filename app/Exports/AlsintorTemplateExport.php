<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AlsintorTemplateExport implements WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'SATKER ID',
            'JENIS BARANG',
            'NUP',
            'KONDISI',
            'KETERANGAN',
        ];
    }
}
