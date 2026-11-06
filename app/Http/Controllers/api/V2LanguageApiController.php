<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\HostChecker;
use App\Http\Controllers\Services\Images;
use App\Models\Game;
use App\Models\Language;
use Illuminate\Http\Request;


class V2LanguageApiController extends Controller
{
    public function columnGen()
    {
        $langs = Language::get('lcid');
        foreach ($langs as $lang) {
            if ($lang['lcid'] <> "en") {
                $game = new Game();
                $game->addNewLanguage($lang['lcid']);
            }
        }
        return response('success', 200);
    }

    public function languageList(Request $request, $version)
    {
        if ($version == 'v2') {
            $links = [];
            $res = [];
            if (array_key_exists('web', $request->all())) {
                if ($request->web == 1) {
                    $languages = Language::where('deleted', 1)
                        ->where('is_displayed_to_user_web', true)
                        ->get()
                        ->makeHidden(['created_at', 'updated_at']);
                } else {
                    $languages = Language::where('deleted', 1)->get()->makeHidden(['created_at', 'updated_at']);
                }
            } else {
                $languages = Language::where('deleted', 1)->get()->makeHidden(['created_at', 'updated_at']);
            }
            foreach ($languages as $language) {
                if ($language->deleted <> 0) {
                    $links["lcid"] = $language->lcid;
                    $links["name"] = $language->name;
                    $links["my_language"] = $language->my_language;
                    $links["play"] = $language->learn;
                    $links["is_displayed_to_user"] = $language->is_displayed_to_user;
                    $links["flag"] = null;
                    if (isset($language->flag)) {
                        $imagesFunctions = new Images();
                        $timeStamps = $language->updated_at->getTimestamp();
                        $mediaUrl = config("values.api_url") . "language/icon/$language->lcid.png";
                        $links["flag"] = $imagesFunctions->apiMediaModel($mediaUrl, $timeStamps, 0);
                    }
                    $res [] = $links;
                }
            }

            return response(json_encode($res, JSON_UNESCAPED_UNICODE), 200);
        } else {
            return response('something wrong', 400);
        }
    }

}