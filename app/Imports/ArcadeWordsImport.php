<?php

namespace App\Imports;

use App\Models\ArcadeWord;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;


class ArcadeWordsImport implements ToCollection
{
    use Importable;

    private $arcade_theme_id;

    public function __construct($id)
    {
        $this->arcade_theme_id = $id;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (isset($row[0])) {
                ArcadeWord::Create([
                    'word' => $row[0],
                    'arcade_theme_id' => $this->arcade_theme_id
                ]);
            }
        }
    }

}
