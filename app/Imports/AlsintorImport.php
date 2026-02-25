<?php

namespace App\Imports;

use App\Models\Alsintor;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AlsintorImport implements ToModel, WithHeadingRow
{
    protected $satker_id;

    public function __construct($satker_id = null)
    {
        $this->satker_id = $satker_id;
    }

    public function model(array $row)
    {
        return new Alsintor([
            'satker_id'    => $this->satker_id ?? $row['satker_id'] ?? null,
            'jenis_barang' => $row['jenis_barang'],
            'nup'          => $row['nup'],
            'kondisi'      => $row['kondisi'] ?? 'Baik',
            'keterangan'   => $row['keterangan'],
        ]);
    }
}
