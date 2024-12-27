<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $modelClass;
    protected $headings;

    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($modelClass, $headings)
    {
        $this->modelClass = $modelClass;
        $this->headings = $headings;
    }

    public function collection()
    {
        return $this->modelClass::all();
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->getStyle('A1:F1')->getFont()->setSize(20);
        $sheet->getStyle('A1:F1')->getBorders()->getAllBorders()->setBorderStyle('thin');
    }
}
