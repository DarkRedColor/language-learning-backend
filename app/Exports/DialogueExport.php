<?php

namespace App\Exports;

use App\Models\Intro;
use App\Models\Personage;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DialogueExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    use Exportable;

    protected $invoices;
    protected $sub_chapter_id;
    private $rowCounter;

    public function __construct(array $invoices, $sub_chapter_id)
    {
        $this->invoices = $invoices;
        $this->sub_chapter_id = $sub_chapter_id;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        $data = $this->getArr();

        return collect($data);

    }

    private function recursiveBuildData($choiceItems, $all)
    {
        $data = $all->whereIn('parent_id', $choiceItems->id)->sortBy('order');
        $response = collect();
        foreach ($data as $item) {
            $this->extracted($item, $all, $response);
        }
        return $response;
    }

    public function headings(): array
    {
        return [
            'id',
            'type',
            'character_name',
            'en',
            $this->invoices[0],

        ];
    }

    public function styles(Worksheet $sheet)
    {

        $data = $this->getArr();

        foreach ($data as $key => $value) {
            switch ($value['type']) {
                case ('btn 1'):
                case ('choice 1'):
                    $sheet->getStyle('B' . ($key + 2))
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB("CEE2F3");
                    $sheet->getStyle('C' . ($key + 2))
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB("CEE2F3");
                    $sheet->getStyle('D' . ($key + 2))
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB("CEE2F3");
                    $sheet->getStyle('E' . ($key + 2))
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB("CEE2F3");
                    break;
                case ('btn 2'):
                case ('choice 2'):
                    $sheet->getStyle('B' . ($key + 2))
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB("D9EAD3");
                    $sheet->getStyle('C' . ($key + 2))
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB("D9EAD3");
                    $sheet->getStyle('D' . ($key + 2))
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB("D9EAD3");
                    $sheet->getStyle('E' . ($key + 2))
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB("D9EAD3");
                    break;
                case ('btn 3'):
                case ('choice 3'):
                    $sheet->getStyle('B' . ($key + 2))
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB("E1C9F2");
                    $sheet->getStyle('C' . ($key + 2))
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB("E1C9F2");
                    $sheet->getStyle('D' . ($key + 2))
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB("E1C9F2");
                    $sheet->getStyle('E' . ($key + 2))
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB("E1C9F2");
                    break;
                case ('btn 4'):
                case ('choice 4'):
                    $sheet->getStyle('B' . ($key + 2))
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB("FEC7E3");
                    $sheet->getStyle('C' . ($key + 2))
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB("FEC7E3");
                    $sheet->getStyle('D' . ($key + 2))
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB("FEC7E3");
                    $sheet->getStyle('E' . ($key + 2))
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB("FEC7E3");
                    break;
                default:
                    break;
            }

        }

        $sheet->getStyle('1')
            ->getFont()
            ->setSize(14)
            ->setBold(true);
        $sheet->getStyle('1')
            ->getFont()
            ->getColor()
            ->setRGB('0000ff');
        $sheet->getColumnDimension('A')
            ->setVisible(false);

    }

    private function extracted($elem, $all, Collection $arr): void
    {
        if ($elem['content_type'] == 3) {
            $ids = [];
            foreach ($elem->next_id as $id) {
                $ids[] = $id[0];
            }
            $choices = $all->whereIn('id', $ids)->sortBy('order');
            foreach ($choices as $choice) {
                if (isset($choice) && !empty($choice))
                    $arr->push($choice);
                if (isset($choice->next_id)) {
                    $recursiveResponse = $this->recursiveBuildData($choice, $all);
                    if (count($recursiveResponse) >= 1) {
                        foreach ($recursiveResponse as $res) {
                            if (isset($res) && !empty($res))
                                $arr->push($res);
                        }
                    } else {
                        if (isset($recursiveResponse) && !empty($recursiveResponse) && count($recursiveResponse) > 0)
                            $arr->push($recursiveResponse);
                    }
                }
            }
        } else {
            if (isset($elem) && !empty($elem))
                $arr->push($elem);
        }
    }

    /**
     * @return array
     */
    private function getArr(): array
    {
        $arr = collect();
        $data = [];
        $choiceId = null;
        $order = null;
        foreach ($this->invoices as $item) {
            $lang = 'text_' . $item;
            $all = Intro::where('sub_chapter_id', $this->sub_chapter_id)->get();
            $personages = Personage::all();
            $firstLevel = $all->whereNull('parent_id')->sortBy('order');

            foreach ($firstLevel as $elem) {
                $this->extracted($elem, $all, $arr);

            }
            $this->rowCounter = count($arr);
            foreach ($arr as $items) {

                if ($items->content_type == 3) {
                    $type = 'btn ' . ($items->order + 1);
                    $choiceId = $items->id;
                    $order = $items->order;
                } else {
                    $type = null;
                }
                if ($items->parent_id == $choiceId && $items->parent_id <> null) {
                    $type = 'choice ' . ($order + 1);
                }
                if (isset($items->personage_id)) {
                    $data[] = [
                        'id' => $items->id,
                        'type' => $type,
                        'character_name' => $personages->where('id', $items->personage_id)->first()->name_en,
                        'text_en' => $items->text_en,
                        $lang => $items->$lang ?? null,

                    ];
                } else {
                    $data[] = [
                        'id' => $items->id,
                        'type' => $type,
                        'character_name' => null,
                        'text_en' => $items->text_en,
                        $lang => $items->$lang ?? null,

                    ];
                }

            }
        }
        return $data;
    }
}
