<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\SubChapter;
use Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Api extends Controller
{
    public function sizesConvert($size)
    {
        $arr = [
            'S' => "_S",
            'M' => "_M",
            'L' => "_L",
            's' => "_S",
            'm' => "_M",
            'l' => "_L",
        ];

        if (array_key_exists($size, $arr)) {
            $size = $arr[$size];
        } else {
            $size = $arr['L'];
        }

        return $size;
    }

    public function twoX()
    {
        return "_2x.png";
    }

    public function treeX()
    {
        return "_3x.png";
    }

    public function sizeBg($size)
    {
        $res = $size;
        if ($size == "ipad" || $size == '.png') {
            $res = "";
        }
        if ($size == $this->twoX() || $size == "2x.png" || $size == "_2x" || $size == '2x') {
            $res = "_2x";
        }
        if ($size == $this->treeX() || $size == '3x' || $size == '_3x') {
            $res = "_3x";
        }
        return $res;

    }

    public function imgNullChck($season, $size, $type, $urlPath, $quiz = false)
    {
        $res = null;
        if (isset($season)) {
            if ($season->$type <> null) {
                $res =  Storage::disk('gcs')->url("$urlPath/". $season['id'] . "$size.webp");
                if ($type == "image_locked") {
                    $res =  Storage::disk('gcs')->url("$urlPath/". $season['id'] . "_locked_$size.webp");
                }
            }

            if ($quiz) {
                $res = Storage::disk('gcs')->url("$urlPath/". $season['name'] . "$size.webp");
                if ($type == "image_locked") {
                    $res = Storage::disk('gcs')->url("$urlPath/". $season['name'] . "_locked_$size.webp");
                }
            }
        }
        return $res;
    }

    public function localiseDataNullCheck($list, $lcid, $type, $v2 = false)
    {
        if ($v2) {
            $res = $list[$type . 'en'];
            if ($list[$type . $lcid] <> null) {
                $res = $list[$type . $lcid];
            }

        } else {
            $res = [
                'value' => $list[$type . 'en'],
                'lcid' => 'en'
            ];

            if ($list[$type . $lcid] <> null) {
                $res =
                    [
                        'value' => $list[$type . $lcid],
                        'lcid' => $lcid
                    ];
            }
        }

        return $res;
    }

    public function lcidExistingCheck($lcid, $web = 0)
    {
        $languages = \App\Models\Language::all();
        $neededLanguage = $languages->where('lcid', $lcid)->first();
        $res = 'en';
        if ($neededLanguage == null) {

            return response(json_encode([
                'code' => 400,
                'message' => 'bad lcid',
            ]));

        }
        if ($web == 1) {
            if ($neededLanguage->is_displayed_to_user_web) {
                $res = $lcid;
            }
        } else {
            if ($neededLanguage->is_displayed_to_user) {
                $res = $lcid;
            }
        }

        return $res;
    }

    public function wordGetData($words, $iso)
    {
        $data = [];
        $wordsList = null;

        shuffle($words);

        foreach ($words as $item) {

            $data['game_type'] = 20;
            $data['mission'] = $item['word'];
            $data['description'] = $item['transcription'];
            $data['answer'] = [[$item['meaning_' . $iso]]];
            $wordsList [] = $data;
        }
        shuffle($wordsList);

        return $wordsList;
    }

    public function nullCheck($data)
    {
        $res = null;
        if (isset($data)) {
            if (is_array($data)) {
                if (isset($data[0])) {
                    if (is_array($data[0])) {
                        if (isset($data[0][0])) {
                            $res = $data;
                        }
                    } else {
                        $res = $data;
                    }
                }
            } else {
                $res = $data;
            }
        }

        return $res;
    }

    public function subTypeCheck($type, $id, $iso)
    {
        if ($type == '1' || $type == '0') {
            return redirect(route('games-api', [$id, $iso]));
        }
        if ($type == '3') {
            return redirect(route('story-api', [$id, $iso]));
        }
        abort(404);
    }
}
