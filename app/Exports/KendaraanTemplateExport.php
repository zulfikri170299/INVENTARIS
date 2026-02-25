<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class KendaraanTemplateExport implements WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'satker_id',
            'jenis_roda',
            'jenis_kendaraan',
            'nup',
            'no_rangka',
            'nopol',
            'kondisi',
            'bahan_bakar',
            'penanggung_jawab',
            'pangkat_nrp',
            'keterangan',
        ];
    }
}
