<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\HostChecker;
use App\Http\Controllers\Services\Version;
use App\Models\Game;
use App\Models\Language;
use Illuminate\Support\Facades\Storage;


class LanguageApiController extends Controller
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

    public function languageList()
    {
        $links = [];
        $res = [];
        $languages = Language::where('deleted', 1)->get()->makeHidden(['created_at', 'updated_at']);
        $hostCheck = new HostChecker();
        $host = $hostCheck->hostCheck(config('values.host'));
        $versionFunctions = new Version();
        foreach ($languages as $language) {
            if ($language->deleted <> 0) {
                $version = $versionFunctions->versionGet($versionFunctions->typeGet('languages'),$language->id);
                $links["lcid"] = $language->lcid;
                $links["name"] = $language->name;
                $links["my_language"] = $language->my_language;
                $links["play"] = $language->learn;
                $links["is_displayed_to_user"] = $language->is_displayed_to_user;
                $links["flag"] = null;
                $links["version"] = $version;
                if (isset($language->flag))
                    $links["flag"] = Storage::disk('gcs')->url("language/$language->lcid");
                $res [] = $links;
            }
        }

        return response(json_encode($res, JSON_UNESCAPED_UNICODE), 200);
    }

}