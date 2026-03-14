<?php

namespace App\Exports;

use App\Models\Alsus;
use App\Models\Alsintor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class AlsusAlsintorExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    protected $satkerId;
    protected $rowNumber = 1;

    public function __construct($satkerId = null)
    {
        $this->satkerId = $satkerId;
    }

    public function collection()
    {
        // Get Alsus data
        $alsusQuery = Alsus::with('satker');
        if ($this->satkerId) {
            $alsusQuery->where('satker_id', $this->satkerId);
        }
        $alsus = $alsusQuery->get();

        // Get Alsintor data
        $alsintorQuery = Alsintor::with('satker');
        if ($this->satkerId) {
            $alsintorQuery->where('satker_id', $this->satkerId);
        }
        $alsintor = $alsintorQuery->get();

        // Combine and group
        $combined = $alsus->concat($alsintor);
        
        $grouped = $combined->groupBy(function($item) {
            return ($item->satker->nama_satker ?? 'Unknown') . '|' . $item->jenis_barang;
        });

        $data = collect();
        foreach ($grouped as $key => $items) {
            list($satker, $barang) = explode('|', $key);
            
            $statusCounts = $items->groupBy('kondisi')->map(function($group) {
                return $group->count();
            });
            
            $data->push([
                'satker' => $satker,
                'jenis_barang' => $barang,
                'baik' => $statusCounts->get('Baik', 0),
                'rusak_ringan' => $statusCounts->get('Rusak Ringan', 0),
                'rusak_berat' => $statusCounts->get('Rusak Berat', 0),
            ]);
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            ['LAPORAN DATA ALSUS DAN ALSINTOR'],
            [''],
            ['NO', 'SATKER', 'NAMA BARANG', 'KONDISI', '', '', 'JUMLAH'],
            ['', '', '', 'BAIK', 'RUSAK RINGAN', 'RUSAK BERAT', '']
        ];
    }

    public function map($row): array
    {
        $currentRow = 4 + $this->rowNumber;
        
        return [
            $this->rowNumber++,
            $row['satker'],
            $row['jenis_barang'],
            $row['baik'],
            $row['rusak_ringan'],
            $row['rusak_berat'],
            '=SUM(D' . $currentRow . ':F' . $currentRow . ')',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Merge title
                $sheet->mergeCells('A1:G1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Merge headers
                $sheet->mergeCells('A3:A4'); // NO
                $sheet->mergeCells('B3:B4'); // SATKER
                $sheet->mergeCells('C3:C4'); // NAMA BARANG
                $sheet->mergeCells('D3:F3'); // KONDISI
                $sheet->mergeCells('G3:G4'); // JUMLAH

                $sheet->getStyle('A3:G4')->getFont()->setBold(true);
                $sheet->getStyle('A3:G4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A3:G4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                
                // Borders for table
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle('A3:G' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // Auto size columns
                foreach (range('A', 'G') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Add Total row
                $totalRow = $lastRow + 1;
                $sheet->setCellValue('A' . $totalRow, 'TOTAL');
                $sheet->mergeCells('A' . $totalRow . ':C' . $totalRow);
                $sheet->getStyle('A' . $totalRow)->getFont()->setBold(true);
                $sheet->getStyle('A' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue('D' . $totalRow, '=SUM(D5:D' . $lastRow . ')');
                $sheet->setCellValue('E' . $totalRow, '=SUM(E5:E' . $lastRow . ')');
                $sheet->setCellValue('F' . $totalRow, '=SUM(F5:F' . $lastRow . ')');
                $sheet->setCellValue('G' . $totalRow, '=SUM(G5:G' . $lastRow . ')');
                $sheet->getStyle('D' . $totalRow . ':G' . $totalRow)->getFont()->setBold(true);
                $sheet->getStyle('A' . $totalRow . ':G' . $totalRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            },
        ];
    }
}
