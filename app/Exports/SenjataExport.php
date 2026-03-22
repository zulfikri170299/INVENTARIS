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
        if ($this->context === 'Personel') {
            return [
                'NO',
                'SATKER',
                'JENIS SENJATA',
                'LARAS',
                'NUP',
                'NO. SENPI',
                'KONDISI',
                'JUMLAH AMUNISI',
                'NAMA',
                'PANGKAT/NRP',
                'MASA SIMSA',
                'KETERANGAN'
            ];
        }

        return [
            'NO',
            'SATKER',
            'JENIS SENJATA',
            'LARAS',
            'NUP',
            'NO. SENPI',
            'KONDISI',
        ];
    }

    public function map($senjata): array
    {
        $this->no++;
        if ($this->context === 'Personel') {
            return [
                $this->no,
                $senjata->satker->nama_satker ?? '-',
                $senjata->jenis_senpi,
                $senjata->laras,
                $senjata->nup ?? '-',
                $senjata->no_senpi ?? '-',
                $senjata->kondisi,
                $senjata->jumlah_amunisi_dibawa ?? 0,
                $senjata->penanggung_jawab ?? '-',
                $senjata->nrp ?? '-',
                $senjata->masa_berlaku_simsa ? \Carbon\Carbon::parse($senjata->masa_berlaku_simsa)->format('d/m/Y') : '-',
                $senjata->keterangan ?? '-'
            ];
        }

        return [
            $this->no,
            $senjata->satker->nama_satker ?? '-',
            $senjata->jenis_senpi,
            $senjata->laras,
            $senjata->nup ?? '-',
            $senjata->no_senpi ?? '-',
            $senjata->kondisi,
        ];
    }
}
