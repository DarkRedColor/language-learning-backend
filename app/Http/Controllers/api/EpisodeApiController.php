<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\Api;
use App\Models\Episode;
use Arr;
use Illuminate\Http\Request;


class EpisodeApiController extends Controller
{
    public function getAllSeasonsEpisodes(Request $request,$rec = false)
    {
        if (array_key_exists('id_season', $request->all())) {
            $id = (int)$request->id_season;
            if ($id == 0) {
                return response('bad id_season', 400);
            }
        } else {
            return response('bad id_season', 400);
        }

        $lcid = "en";
//        $size = '3'; will need for image

        if (array_key_exists('lcid',$request->all())){
            $lcid = $request->lcid;
        }

//        if (array_key_exists('icons_scale',$request->all())){ will need for image
//            $size = $request->icons_scale;
//        }

        $apiFunctions = new Api();

        if ($apiFunctions->lcidExistingCheck($lcid) <> $lcid) {
            return response('bad lcid', 400);
        }

        $episodes = Episode::where('season_id', $id)
            ->whereNotNull('order')
            ->where('is_displayed_to_user',1)
            ->orderBy('order')
            ->get();

        if (count($episodes) === 0) {
            return [];
        }


        $datas = [];
        foreach ($episodes as $episode) {

            $data['id'] = $episode->id;
            $data['description'] = $apiFunctions->localiseDataNullCheck($episode, $lcid, 'name_');
            $data['order'] = $episode->order;
            $data['image'] = null;

//            here will need size
//            if (!$data['image'] == null) {
//                $data['image'] = $episode['image'] . $this->size($size);
//            }
            if ($rec){
                $chapterF = new ChapterApiController();
                $request['id_episode'] = $episode->id;
                $data['chapters'] = $chapterF->getAllEpisodesChapters($request,true);
            }

            $datas [] = $data;
        }
        if ($rec){
            return $datas;
        }
        return response(json_encode($datas, JSON_UNESCAPED_UNICODE));
    }

}
