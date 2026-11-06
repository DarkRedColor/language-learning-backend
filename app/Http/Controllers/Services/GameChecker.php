<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\GameDescription;
use App\Models\GameType;
use App\Models\Theory;
use Illuminate\Http\Request;

class GameChecker extends Controller
{
    public function gameCheck($item, $description, $iso, $theory, $gameTypeName)
    {
        $service = new Games();
        $data = null;
        $logicArray = [
            "make a sentence" => $service->makeASentence(),
            "type words" => $service->typeWords(),
            "type answer" => $service->typeAnswer(),
            'insert words' => $service->insertWords(),
            "erudite" => $service->erudite(),
            "millionaire" => $service->millionaire(),
            "link words" => $service->linkWords(),
        ];

        $function = \Str::camel($gameTypeName['name']);


        if (isset($logicArray[$gameTypeName['name']])) {
            $data = $this->$function($item, $description, $iso, $theory);
        }
        else {
            abort(500);
        }

        return $data;
    }

    public function gameDataGet($games, $iso)
    {
        shuffle($games);

        foreach ($games as $item) {
            $gameTypeName = GameType::where('id', $item['type_id'])->get('name')->toArray();
            $gameTypeName = $gameTypeName[0];
            $description = GameDescription::where('id', $item['description_id'])->get(['description_' . $iso, 'description_en'])->toArray();
            $description = $description[0];

            $theory = Theory::where('id', $item['theory_id'])->get('description_' . $iso)->toArray();
            $theory = $theory[0];

            $datas [] = (new GameChecker)->gameCheck($item, $description, $iso, $theory, $gameTypeName);

            if (count($datas) === 15) {
                break;
            }
        }

        shuffle($datas);

        return $datas;
    }
}

