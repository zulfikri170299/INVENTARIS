<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SenjataExport implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize
{
    protected $query;
    protected $no = 0;
    protected $context;

    public function __construct($query, $context = 'Gudang')
    {
        $this->query = $query;
        $this->context = $context;
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        $headings = [
            'NO',
            'SATKER',
            'JENIS SENPI',
            'LARAS',
            'NUP',
            'NO SENPI',
            'KONDISI',
            'KETERANGAN',
        ];

        if ($this->context === 'Personel') {
            array_splice($headings, 7, 0, [
                'STATUS PENYIMPANAN',
                'PENANGGUNG JAWAB',
                'NRP',
                'MASA BERLAKU SIMSA',
                'JENIS AMUNISI DIBAWA',
                'JUMLAH AMUNISI DIBAWA'
            ]);
        }

        return $headings;
    }

    public function map($senjata): array
    {
        $this->no++;
        $data = [
            $this->no,
            $senjata->satker->nama_satker ?? '-',
            $senjata->jenis_senpi,
            $senjata->laras,
            $senjata->nup,
            $senjata->no_senpi,
            $senjata->kondisi,
            $senjata->keterangan,
        ];

        if ($this->context === 'Personel') {
            array_splice($data, 7, 0, [
                $senjata->status_penyimpanan,
                $senjata->penanggung_jawab,
                $senjata->nrp,
                $senjata->masa_berlaku_simsa,
                $senjata->jenis_amunisi_dibawa,
                $senjata->jumlah_amunisi_dibawa
            ]);
        }

        return $data;

    }
}
