<?php

namespace App\Imports;

use App\Models\Intro;
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

class DialogueImport implements ToCollection, WithUpsertColumns, WithHeadingRow
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
                if ($localisation <> null) {
                    $dialogue = Intro::find($row['id']);
                    if (isset($dialogue)) {
                        $dialogue->update([
                            'text_' . $localisation => $row[$localisation]
                        ]);
                    } else {
                        abort(400,'Not found your id');
                    }
                }
            }
        }
    }

    public function upsertColumns()
    {
        return 'text_en';
    }
}
