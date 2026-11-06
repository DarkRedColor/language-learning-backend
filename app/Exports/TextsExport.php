<?php

namespace App\Exports;

use App\Models\Text;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Writer;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;


class TextsExport implements FromCollection, WithColumnWidths, WithTitle, WithHeadings, WithStyles
{
    use RegistersEventListeners;
    use Exportable;

    protected $invoices;
    private $exportExcelPassword, $needPasswordProtectExcel;
    private $rowCounter;

    public function __construct(array $invoices)
    {
        $this->invoices = $invoices;
    }


    public function collection(): Collection
    {
        $arr = [];
        foreach ($this->invoices as $item) {
            $lang = 'name_' . $item;
            $arr[] = Text::where($lang)->get(['name_en', $lang]);
            $this->rowCounter = count($arr[0]);
        }
        return collect($arr);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 55,
            'B' => 45,
        ];
    }

    public function title(): string
    {
        $lang = 'name_' . $this->invoices[0];
        return count(Text::where($lang)->get(['name_en', $lang])) . "words";
    }

    public function headings(): array
    {
        return [
            'en',
            $this->invoices[0],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'EB2B02'],
                ],
            ],
            "A" => [
                'protection' => [
                    'password' => 'meow'
                ]
            ]
        ];
    }

    public function registerEvents(): array
    {
        return [
            BeforeExport::class => function (BeforeWriting $event) {
                Writer::macro('setPassword', function (Writer $writer, string $password) {
                    $writer->getDelegate()->getActiveSheet()->getProtection()->setSheet(true);
                    $writer->getDelegate()->getActiveSheet()->getProtection()->setPassword($password);
                });
                $event->writer->setPassword($this->exportExcelPassword);
            }
        ];
    }
}
