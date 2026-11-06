<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\ArcadeGame;
use App\Models\ArcadePage;
use App\Models\ArcadeWord;


class ArcadeGames extends Controller
{
    private function dataBuild($type, $size, $lcid, $arcadeType)
    {
        $introductionData = null;
        $notificationData = null;
        $endGameData = null;
        $winAlertData = null;
        $data = ArcadeGame::with('arcadeThemes')->where('type', $type)->first();
//        $pages = null;
        if (isset($data) && !empty($data)) {
            $pages = ArcadePage::where('arcade_game_id', $data->id)->get();
            if (isset($pages) && !empty($pages)) {
                foreach ($pages as $page) {
                    switch ($page->type) {
                        case (0):
                            $page['background_id'] = $data->game_background_id;
                            if (isset($page) && !empty($page) && !is_bool($page))
                                $introductionData = $this->arcadeGamesTypeDataGet($page->type, $page, $lcid, $pages, $size);
                            break;
                        case (1):
                            $page['background_id'] = $data->game_background_id;
                            if (isset($page) && !empty($page) && !is_bool($page))
                                $notificationData = $this->arcadeGamesTypeDataGet($page->type, $page, $lcid, $pages, $size, $notificationData);
                            break;
                        case (2):
                            $page['background_id'] = $data->game_background_id;
                            if (isset($page) && !empty($page) && !is_bool($page))
                                $endGameData = $this->arcadeGamesTypeDataGet($page->type, $page, $lcid, $pages, $size);
                            break;
                        default:
                            $page['background_id'] = $data->game_background_id;
                            if (isset($page) && !empty($page) && !is_bool($page))
                                $winAlertData = $this->arcadeGamesTypeDataGet($page->type, $page, $lcid, $pages, $size, $winAlertData);
                            break;
                    }
                }
            }
            $gameType = $type;
            $splashBgId['background_id'] = $data->start_background_id;
            $defaultBgId['background_id'] = $data->game_background_id;
            $backgroundFunctions = new Backgrounds();
            $splashBg = $backgroundFunctions->backgroundV2Get(json_decode(json_encode($splashBgId), false), $size);
            $defaultBg = $backgroundFunctions->backgroundV2Get(json_decode(json_encode($defaultBgId), false), $size);
            $gameDataArr = ['data' => $this->gameDataBuild($data, $lcid)];
            if ($arcadeType == 0)
                $typeFast = [
                    'type_fast_data' => $gameDataArr,
                    'replay_character' => [
                        'id' => 5,
                        'position' => 1,
                        'emotion_id' => 7
                    ]
                ];
            else
                $typeFast = [
                    'find_words_data' => $gameDataArr,
                    'replay_character' => [
                        'id' => 5,
                        'position' => 1,
                        'emotion_id' => 7
                    ]
                ];


            $arcadeGameDataRes = [
                'game_type' => $gameType,
                'splash_background' => $splashBg,
                'default_background' => $defaultBg,
                'introduction' => $introductionData,
                'notification' => $notificationData,
                'end_game' => $endGameData,
                'game_result' => $winAlertData,
                'game_data' => $typeFast,
            ];

            return $arcadeGameDataRes;
        } else
            return null;
    }

    public function dataGet($type, $size, $lcid)
    {
        $response = response('no content', 404);
        switch ($type) {
            case (1):
            case (0):
                $res = $this->dataBuild($type, $size, $lcid, $type);
                break;
            default:
                $res[] = $this->dataBuild(0, $size, $lcid, 0);
                $res[] = $this->dataBuild(1, $size, $lcid, 1);
                break;
        }
        if ($res <> null) {
            if (is_array($res) && array_key_exists(0, $res) && array_key_exists(1, $res)) {
                if ($res[0] == null && $res[1] == null) {
                    $response = response('no content', 404);
                } else {
                    $response = json_encode($res, JSON_INVALID_UTF8_SUBSTITUTE);
                }
            } else
                $response = json_encode($res, JSON_INVALID_UTF8_SUBSTITUTE);
        } else
            $response = response('no content', 404);

        return $response;
    }

    public function arcadeGamesTypeDataGet($type, $item, $lcid, $pages, $size = null, $winAlertData = null)
    {
        switch ($type) {
            case (0):
                $res = $this->introductionDataBuild($item, $pages, $size, $lcid);
                break;
            case (1):
                $res = $this->notificationDataBuild($item, $pages, $size, $lcid, $winAlertData);

                break;
            case (2):
                $res = $this->endGameDataBuild($item, $pages, $size, $lcid);

                break;
            default:
                $res = $this->winAlertDataBuild($item, $pages, $size, $lcid, $winAlertData);
                break;
        }

        return $res;
    }

    private function introductionDataBuild($item, $pages, $size, $lcid)
    {

        [$character, $background, $id, $nextId, $text] = $this->basicDataBuild($item, $pages, $size, $lcid);
        return [
            'id' => $id,
            'nextId' => $nextId,
            'text' => $text,
            'background' => $background,
            'characters' => $character
        ];

    }

    private function notificationDataBuild($item, $pages, $size, $lcid, $notificationData)
    {
        [$character, $background, $id, $nextId, $text] = $this->basicDataBuild($item, $pages, $size, $lcid);

        $arrData = [
            'id' => $id,
            'nextId' => $nextId,
            'text' => $text,
            'background' => $background,
            'characters' => $character
        ];
        if ($notificationData == null) {
            $notificationData = [
                'default' => [],
                'tutorial' => [],
            ];
        }

        if ($item->add_type == 0) {
            $notificationData['default'] = $arrData;
        } else {
            $notificationData['tutorial'] = $arrData;

        }
        return $notificationData;
    }

    private function endGameDataBuild($item, $pages, $size, $lcid)
    {
        [$character, $background, $id, $nextId, $text] = $this->basicDataBuild($item, $pages, $size, $lcid);

        return [
            'id' => $id,
            'nextId' => $nextId,
            'text' => $text,
            'background' => $background,
            'characters' => $character
        ];

    }

    private function winAlertDataBuild($item, $pages, $size, $lcid, $winAlertData)
    {
        [$character, $background, $id, $nextId, $text] = $this->basicDataBuild($item, $pages, $size, $lcid);

        $arrData = [
            'id' => $id,
            'nextId' => $nextId,
            'text' => $text,
            'background' => $background,
            'characters' => $character
        ];
        if ($winAlertData == null) {
            $winAlertData = [
                'default' => [],
                'tutorial' => [],
            ];
        }
        if ($item->add_type == 0) {
            $winAlertData['default'][] = $arrData;
        } else {
            $winAlertData['tutorial'][] = $arrData;
        }
        return $winAlertData;
    }

    private function basicDataBuild($item, $pages, $size, $lcid)
    {
        $arrayModelFunctions = new ArrayModel();
        $backgroundFunctions = new Backgrounds();
        $textFunctions = new Text();

        $character = $arrayModelFunctions->characterArrayGet($item);
        $background = $backgroundFunctions->backgroundV2Get($item, $size);
        $id = $item->id;
        $nextPage = $pages->where('order', $item->order + 1)->first();
        $nextId = $nextPage->id ?? null;
        $text = $textFunctions->onlyLocalisationGameGet($lcid, 'text', $item);

        return [$character, $background, $id, $nextId, $text];
    }

    public function gameDataBuild($value, $lcid)
    {
        $datas = [];
        $textFunctions = new Text();
        foreach ($value->arcadeThemes as $item) {
            $title = $textFunctions->onlyLocalisationGameGet($lcid, 'name', $item);
            $wordsCollection = ArcadeWord::where('arcade_theme_id', $item->id)->get('word');
            $words = [];
            foreach ($wordsCollection as $word) {
                $words[] = $word->word;
            }
            $data = [
                'id' => $item->id,
                'title' => $title,
                'words' => $words
            ];
            $datas[] = $data;
        }
        return $datas;

    }

}
