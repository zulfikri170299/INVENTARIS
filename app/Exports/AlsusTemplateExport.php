<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AlsusTemplateExport implements WithHeadings, ShouldAutoSize
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
