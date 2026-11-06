<?php

namespace App\Imports;

use App\Models\Language;
use App\Models\Text;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpsertColumns;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Illuminate\Support\Collection;

HeadingRowFormatter::default('none');

class TextsImport implements ToCollection, WithUpsertColumns, WithHeadingRow
{
    use Importable;

    public function collection(Collection $rows)
    {
        $arr = [];
        $languages = Language::all();
        $localisation = null;
        foreach ($languages as $language) {
            if ($language->lcid <> 'en') {
                $arr[] = $language->lcid;
            }
        }
        foreach ($rows as $row) {
            foreach ($arr as $item) {
                if ($row->has($item)) {
                    $localisation = $item;
                }
            }
            if (isset($row['en'])) {


                $key = $row['en'];
                $key = Str::replace(' ', '_', $key);
                if (strlen($key) > 30) {
                    $key = substr($key, 0, 30);
                }

                $texts = Text::all();

                if (count($texts->where('name_en', $row['en'])) > 0) {
                    if ($localisation <> null) {
                        Text::where('name_en', $row['en'])->update([
                            'name_' . $localisation => $row[$localisation]
                        ]);
                    }
                } else {
                    Text::Create([
                        'name_en' => $row['en'],
                        'key' => (new \App\Http\Controllers\Services\Text())->unicalCheck($texts, $key, 1, 2,'key')
                    ]);

                    if ($localisation <> null) {
                        Text::where('name_en', $row['en'])->update([
                            'name_' . $localisation => $row[$localisation]
                        ]);
                    }
                }
            }
        }
    }

    public function upsertColumns()
    {
        return 'name_en';
    }

}
