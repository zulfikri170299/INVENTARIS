<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class KendaraanTemplateExport implements WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'SATKER ID',
            'JENIS RODA',
            'JENIS KENDARAAN',
            'NUP',
            'TAHUN PEMBUATAN',
            'NO MESIN',
            'NO RANGKA',
            'NOPOL',
            'KONDISI',
            'BAHAN BAKAR',
            'PENANGGUNG JAWAB',
            'PANGKAT NRP',
            'KETERANGAN',
        ];
    }
}
