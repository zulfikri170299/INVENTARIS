<?php

namespace App\Imports;

use App\Models\Senjata;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SenjataImport implements ToModel, WithHeadingRow
{
    protected $satker_id;
    protected $status_default;

    public function __construct($satker_id = null, $status_default = 'Gudang')
    {
        $this->satker_id = $satker_id;
        $this->status_default = $status_default;
    }

    public function model(array $row)
    {
        return new Senjata([
            'satker_id'             => $this->satker_id ?? $row['satker_id'] ?? null,
            'jenis_senpi'           => $row['jenis_senpi'] ?? $row['jenis_senjata'],
            'laras'                 => $row['laras'],
            'nup'                   => $row['nup'],
            'no_senpi'              => $row['no_senpi'],
            'kondisi'               => $row['kondisi'] ?? 'Baik',
            'status_penyimpanan'    => $row['penyimpanan'] ?? $row['status_penyimpanan'] ?? $this->status_default,
            // Restore person-related fields
            'penanggung_jawab'      => $row['nama'] ?? $row['penanggung_jawab'] ?? null,
            'nrp'                   => $row['pangkat_nrp'] ?? $row['nrp'] ?? null,
            'masa_berlaku_simsa'    => isset($row['masa_simsa']) ? \Carbon\Carbon::parse($row['masa_simsa']) : ($row['masa_berlaku_simsa'] ?? null),
            'jumlah_amunisi_dibawa' => $row['jumlah_amunisi'] ?? $row['jumlah_amunisi_dibawa'] ?? 0,
        ]);
    }
}
