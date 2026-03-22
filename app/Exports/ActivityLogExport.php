<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ActivityLogExport implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize
{
    protected $query;

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
            'WAKTU',
            'USER',
            'ROLE',
            'AKTIVITAS',
            'DETAIL',
            'MODUL',
            'IP ADDRESS',
            'INFO PERANGKAT',
        ];
    }

    public function map($log): array
    {
        return [
            $log->created_at->format('d/m/Y H:i'),
            $log->user->name ?? 'System',
            $log->user->role ?? '-',
            $log->activity,
            $log->description,
            $log->module,
            $log->ip_address,
            $log->user_agent,
        ];
    }
}
