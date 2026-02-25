<?php

namespace App\Imports;

use App\Models\Satker;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SatkerImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['nama_satker'])) {
            return null;
        }

        return new Satker([
            'nama_satker' => $row['nama_satker'],
        ]);
    }
}
