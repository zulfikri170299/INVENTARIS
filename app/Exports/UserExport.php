<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class UserExport implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize
{
    protected $query;
    protected $no = 0;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'NO',
            'SATKER',
            'NAMA',
            'EMAIL',
            'ROLE',
        ];
    }

    public function map($user): array
    {
        $this->no++;
        return [
            $this->no,
            $user->satker->nama_satker ?? 'Global / Admin',
            $user->name,
            $user->email,
            $user->role,
        ];
    }
}
