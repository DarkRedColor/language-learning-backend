<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\Api;
use App\Http\Controllers\Services\Version;
use App\Models\Language;
use App\Models\Text;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class TextApiController extends Controller
{
    public function create(Request $request)
    {
        $texts = $request->all();
        $isos = Language::get('lcid');

        if ($texts == null) {
            return response('bad content format, need this format ["key": {"value": "text", "lcid": "code"}], get null.', 400);
        }
        foreach ($texts as $text) {

            [$keys, $values] = Arr::divide($text);
            $dbKey = $keys;
            $textNames = [];
            foreach ($values as $value) {
                $textNames = $this->textCreate($value, $isos, $textNames);
            }
            $textNames['key'] = $dbKey[0];

            Text::create($textNames);
        }

        return response('success', 200);
    }

    public function textGet(Request $request)
    {
        $iso = "en";

        if (array_key_exists('lcid', $request->all())) {
            $iso = $request->lcid;
        }

        $texts = Text::all()->makeHidden(['created_at', 'updated_at']);
        $textLists = [];

        $web = 0;
        if (array_key_exists('web',$request->all())){
            $web = $request->web;
        }

        $apiFunctions = new Api();
        if ($apiFunctions->lcidExistingCheck($iso,$web) <> $iso) {
            return response('bad lcid', 400);
        }
        $versionFunctions = new Version();

        foreach ($texts as $text) {
            $isoName = 'name_' . $iso;
            $version = $versionFunctions->versionGet($versionFunctions->typeGet('text'), $text->id);
            if (!$text[$isoName] == null) {
                $textLists[$text['key']] = [
                    'lcid' => $iso,
                    'value' => $text[$isoName],
                    'version' => $version
                ];
            } else {
                $textLists[$text['key']] = [
                    'lcid' => 'en',
                    'value' => $text['name_en'],
                    'version' => $version
                ];
            }
        }
        return response(json_encode($textLists, JSON_UNESCAPED_UNICODE), 200);
    }

    private function textCreate($value, $isos, $textNames)
    {
        foreach ($value as $item) {
            $i = 0;
            foreach ($isos as $iso) {
                if ($item['lcid'] == $iso['lcid']) {
                    $i += 1;
                }
            }
            if ($i === 0) {
                return response('unidentified lcid', 400);
            } else {
                $textNames["name_" . $item["lcid"]] = $item['value'];
            }
        }
        return $textNames;
    }
}
