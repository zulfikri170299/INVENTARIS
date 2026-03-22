<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SatkerTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            ['1', 'Polsek Metro Penjaringan'],
            ['2', 'Polsek Metro Gambir'],
        ];
    }

    public function headings(): array
    {
        return [
            'NO',
            'NAMA SATKER',
        ];
    }
}
