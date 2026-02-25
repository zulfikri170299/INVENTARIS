<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SatkerTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            ['Polsek Metro Penjaringan'],
            ['Polsek Metro Gambir'],
        ];
    }

    public function headings(): array
    {
        return [
            'nama_satker',
        ];
    }
}
