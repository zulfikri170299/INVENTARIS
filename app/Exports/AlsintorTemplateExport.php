<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AlsintorTemplateExport implements WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'satker_id',
            'jenis_barang',
            'nup',
            'kondisi',
            'keterangan',
        ];
    }
}
