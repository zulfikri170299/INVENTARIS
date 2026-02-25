<?php

namespace App\Imports;

use App\Models\Kendaraan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KendaraanImport implements ToModel, WithHeadingRow
{
    protected $satker_id;

    public function __construct($satker_id = null)
    {
        $this->satker_id = $satker_id;
    }

    public function model(array $row)
    {
        return new Kendaraan([
            'satker_id'       => $this->satker_id ?? $row['satker_id'] ?? null,
            'jenis_roda'      => $row['jenis_roda'] ?? 'R4',
            'jenis_kendaraan' => $row['jenis_kendaraan'],
            'nup'             => $row['nup'],
            'no_rangka'       => $row['no_rangka'],
            'nopol'           => $row['nopol'],
            'kondisi'         => $row['kondisi'] ?? 'Baik',
            'bahan_bakar'     => $row['bahan_bakar'],
            'penanggung_jawab' => $row['penanggung_jawab'],
            'nrp'             => $row['pangkat_nrp'],
            'keterangan'      => $row['keterangan'],
        ]);
    }
}
