<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SenjataTemplateExport implements WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'satker_id',
            'jenis_senpi',
            'laras',
            'nup',
            'no_senpi',
            'kondisi',
            'penanggung_jawab',
            'pangkat_nrp',
            'status_penyimpanan',
            'masa_berlaku_simsa',
            'keterangan',
        ];
    }
}
