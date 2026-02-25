<?php

namespace App\Imports;

use App\Models\Senjata;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SenjataImport implements ToModel, WithHeadingRow
{
    protected $satker_id;

    public function __construct($satker_id = null)
    {
        $this->satker_id = $satker_id;
    }

    public function model(array $row)
    {
        return new Senjata([
            'satker_id'          => $this->satker_id ?? $row['satker_id'] ?? null,
            'jenis_senpi'        => $row['jenis_senpi'],
            'laras'              => $row['laras'],
            'nup'                => $row['nup'],
            'no_senpi'           => $row['no_senpi'],
            'kondisi'            => $row['kondisi'] ?? 'Baik',
            'penanggung_jawab'   => $row['penanggung_jawab'],
            'nrp'                => $row['pangkat_nrp'],
            'status_penyimpanan' => $row['status_penyimpanan'],
            'masa_berlaku_simsa' => $row['masa_berlaku_simsa'] ?? null,
            'keterangan'         => $row['keterangan'],
        ]);
    }
}
