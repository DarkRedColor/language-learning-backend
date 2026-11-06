<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\Api;
use App\Http\Controllers\Services\HostChecker;
use App\Models\Background;
use App\Models\Chapter;
use App\Models\Episode;
use App\Models\Season;
use Arr;
use Illuminate\Http\Request;

class ChapterApiController extends Controller
{
    public function getAllEpisodesChapters(Request $request, $rec = false)
    {
        if (array_key_exists('id_episode', $request->all())) {
            $id = (int)$request->id_episode;
            if ($id == 0) {
                return response('bad id_episode', 400);
            }
        } else {
            return response('bad id_episode', 400);
        }
        $lcid = "en";
        $size = 'L';
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

//        if ($this->theFirstOneCheck($id)) {
//            $chapters = Chapter::where('episode_id', $id)
//                ->whereNotNull('order')
//                ->orderBy('order')
//                ->get();
//
//            $intro = Chapter::where('episode_id', 0)->get();
//            $intro = $intro[0];
//
//            push intro into array of chapters
//
//            $chapters->push($intro);
//        } else {
        $chapters = Chapter::where('episode_id', $id)
            ->whereNotNull('order')
            ->where('is_displayed_to_user', true)
            ->orderBy('order')
            ->get();
//        }

        if (count($chapters) === 0) {
            return [];
        }
        $hostCheck = new HostChecker();
        $host = $hostCheck->hostCheck(config('values.host'));

        $datas = [];

        foreach ($chapters as $chapter) {
            $data['id'] = $chapter->id;
            $data['description'] = $apiFunctions->localiseDataNullCheck($chapter, $lcid, 'name_');
            $data['order'] = $chapter->order;
            $data['type'] = $chapter->type;
            $data['thumbnail_unlocked'] = $apiFunctions->imgNullChck($chapter, $apiFunctions->sizesConvert($size), 'image_unlocked', 'chapter/image/' . $host . "/");
            $data['thumbnail_locked'] = $apiFunctions->imgNullChck($chapter, $apiFunctions->sizesConvert($size), 'image_locked', 'chapter/image/' . $host . "/");
            if ($rec) {
                $chapterF = new SubChapterApiController();
                $request['id_chapter'] = $chapter->id;
//                if (isset($chapter->transform)) {
//                    $data['transform'] = json_decode($chapter->transform);
//                } else {
//                    $data['transform'] = null;
//                }
                $data['scenes'] = $chapterF->getAllChaptersSubChapters($request, true);
            }
            $datas [] = $data;
        }
        if ($rec) {
            return $datas;
        }
        return response(json_encode($datas, JSON_UNESCAPED_UNICODE));
    }

    // Check if selected chapter is in first episode of first season
//    private function theFirstOneCheck($id)
//    {
//        $res = false;
//
//        $episode = Episode::find($id);
//        if (isset($episode)) {
//            if ($episode->order == 0) {
//                $season = Season::find($episode->season_id);
//                if (isset($season)) {
//                    if ($season->order == 0) {
//                        $res = true;
//                    }
//                }
//            }
//        }
//        return $res;
//    }
}
