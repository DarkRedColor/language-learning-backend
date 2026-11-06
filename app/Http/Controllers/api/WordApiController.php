<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\Api;
use App\Http\Controllers\Services\GameChecker;
use App\Models\Game;
use App\Models\GameDescription;
use App\Models\GameType;
use App\Models\SubChapter;
use App\Models\Theory;

class WordApiController extends Controller
{
    private function datasForming($games, $words, $iso)
    {
        $datas = [];

        if (!count($games) == 0) {
            $datas = (new GameChecker)->gameDataGet($games, $iso);
        }

        if (!count($words) == 0) {

            $wordsList = ((new Api)->wordGetData($words, $iso));

            foreach ($wordsList as $item) {
                array_unshift($datas, $item);
            }
        }

        if (count($words) == 0 && count($games) == 0) {
            $res = [];
        } else {

            for ($i = 0; $i < count($datas); $i++) {
                $datas[$i]['order'] = $i;
            }

            $res = response(json_encode($datas, JSON_UNESCAPED_UNICODE));
        }
        return $res;
    }

    public function getGamesAndWordsOfSubchapterWordEntity($id, $iso)
    {
        if ($iso === 'en') {
            $res = response('lcid cant be "en"', 400);
        } else {
            $sub = SubChapter::find($id);
            if ($sub == null) {
                $res = [];
            } else {

                (new Api())->subTypeCheck($sub->type, $id, $iso);

                $games = Game::where('sub_chapter_id', $id)
                    ->whereNotNull('order')
                    ->orderBy('order')
                    ->get([
                        'id',
                        'mission_en',
                        'mission_' . $iso,
                        'name_en',
                        'link_words',
                        'linked_words_' . $iso,
                        'order',
                        'description_id',
                        'theory_id',
                        'type_id',
                        'wrong',
                        'answer',
                    ])
                    ->makeHidden(['created_at', 'updated_at'])
                    ->toArray();

                $res = $this->datasForming($games, $sub->words()->get()->toArray(), $iso);
            }
        }
        return $res;
    }


}
