<?php

namespace App\Imports;

use App\Models\Amunisi;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AmunisiImport implements ToModel, WithHeadingRow
{
    protected $satker_id;

    public function __construct($satker_id = null)
    {
        $this->satker_id = $satker_id;
    }

    public function model(array $row)
    {
        return new Amunisi([
            'satker_id'          => $this->satker_id ?? $row['satker_id'] ?? null,
            'jenis_amunisi'      => $row['jenis_amunisi'],
            'jumlah'             => $row['jumlah'] ?? 0,
            'status_penyimpanan' => $row['status_penyimpanan'] ?? 'Gudang',
            'keterangan'         => $row['keterangan'] ?? null,
        ]);
    }
}
