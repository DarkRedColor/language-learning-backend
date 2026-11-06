<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\EpisodeController;
use App\Http\Controllers\Services\Api;
use App\Http\Controllers\Services\HostChecker;
use App\Models\Season;
use Arr;
use Illuminate\Http\Request;


class SeasonApiController extends Controller
{
    /**
     *
     *
     * @return \Illuminate\Http\Response
     */

    public function allSeasons(Request $request)
    {
        $lcid = 'en';
        $size = 'L';
        $ids = null;
        $seasons = null;

        if (array_key_exists('lcid', $request->all())) {
            $lcid = $request->lcid;
        }

        if (array_key_exists('icons_scale', $request->all())) {
            $size = $request->icons_scale;
        }

        $apiFunctions = new Api();

        if ($apiFunctions->lcidExistingCheck($lcid) <> $lcid) {
            return response('bad lcid', 400);
        }

        if (array_key_exists('list', $request->all())) {
            $ids = explode(',', $request->list);
        }

        if (isset($ids)) {
            if ($ids[0] == '-1') {

                $seasons = Season::whereNotNull('order')
                    ->where('is_displayed_to_user', 1)
                    ->orderBy('order')
                    ->get();
            } else {
                $seasons = Season::whereNotNull('order')
                    ->where('is_displayed_to_user', 1)
                    ->whereIn('id', $ids)
                    ->orderBy('order')
                    ->get();
            }
        }else{
            $seasons = Season::whereNotNull('order')
                ->where('is_displayed_to_user', 1)
                ->orderBy('order')
                ->get();
        }

        $datas = [];
        $hostCheck = new HostChecker();
        $host = $hostCheck->hostCheck(config('values.host'));

        if (isset($seasons)) {
            foreach ($seasons as $key => $season) {
                $data['id'] = $season->id;
                $data['description'] = $apiFunctions->localiseDataNullCheck($season, $lcid, 'name_');
                $data['order'] = $key;
                $data['thumbnail_unlocked'] = $apiFunctions->imgNullChck($season, $apiFunctions->sizesConvert($size), 'image_unlocked', 'season');
                $data['thumbnail_locked'] = $apiFunctions->imgNullChck($season, $apiFunctions->sizesConvert($size), 'image_locked', 'season' );
                $data['background_original'] = null;
                $data['background_filtered'] = null;
                if (isset($ids)){
                    $episodesF = new EpisodeApiController();
                    $request['id_season'] = $season->id;
                    $data['episodes'] = $episodesF->getAllSeasonsEpisodes($request,true);
                }
                $datas [] = $data;
            }
        }

        return response(json_encode($datas, JSON_UNESCAPED_UNICODE));
    }

}
