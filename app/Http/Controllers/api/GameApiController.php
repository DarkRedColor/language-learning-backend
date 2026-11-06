<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\GameChecker;
use App\Models\Game;
use App\Models\GameDescription;
use App\Models\GameType;
use App\Models\SubChapter;
use App\Models\Theory;

class GameApiController extends Controller
{
    public function getGame($id, $iso)

    {
        if ($iso === 'en') {
            $res = response('LCID cant be "en" on games', 400);
        } else {
            $games = Game::where('id', $id)
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

            if (count($games) === 0) {
                $res = [];
            } else {

                $datas = [];

                foreach ($games as $item) {
                    $gameTypeName = GameType::where('id', $item['type_id'])->get('name')->toArray();
                    $gameTypeName = $gameTypeName[0];
                    $description = GameDescription::where('id', $item['description_id'])->get(['description_' . $iso, 'description_en'])->toArray();
                    $description = $description[0];

                    $theory = Theory::where('id', $item['theory_id'])->get('description')->toArray();
                    $theory = $theory[0];

                    $datas [] = (new GameChecker)->gameCheck($item, $description, $iso, $theory, $gameTypeName);

                }

                $res = response(json_encode($datas, JSON_UNESCAPED_UNICODE));
            }
        }
        return $res;
    }

    public
    function getAllSubChaptersGames($id, $iso)
    {
        if ($iso === 'en') {
            $res = response('LCID cant be "en" on games', 400);
        } else {
            $sub = SubChapter::find($id);
            if ($sub->type === 2) {
                $res = redirect(route('word-api', [$id, $iso]));
            } else {
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

                if (count($games) === 0) {
                    $res = response('', 204);
                } else {
                    $datas = (new GameChecker)->gameDataGet($games, $iso);

                    for ($i = 0; $i < count($datas); $i++) {
                        $datas[$i]['order'] = $i;
                    }

                    $res = response(json_encode($datas, JSON_UNESCAPED_UNICODE));
                }
            }
        }
        return $res;
    }
}
