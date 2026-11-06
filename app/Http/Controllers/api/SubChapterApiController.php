<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\Api;
use App\Http\Controllers\Services\HostChecker;
use App\Models\Chapter;
use App\Models\Learning;
use App\Models\Quiz;
use App\Models\SubChapter;
use Arr;
use Illuminate\Http\Request;

class SubChapterApiController extends Controller
{
    public function getAllChaptersSubChapters(Request $request, $rec = false)
    {
        if (array_key_exists('id_chapter', $request->all())) {
            $id = (int)$request->id_chapter;
            if ($id == 0) {
                return response('bad id_chapter', 400);
            }
        } else {
            return response('bad id_chapter', 400);
        }
        $size = "L";
        $lcid = "en";
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
        $chapter = Chapter::find($id);

        $subChapters = SubChapter::where('chapter_id', $id)
            ->whereNotNull('order')
            ->Where('is_displayed_to_user', true)
            ->where('type', 0)
            ->orderBy('order')
            ->get();

        if (count($subChapters) === 0) {

            return [];

        }
        $hostCheck = new HostChecker();
        $host = $hostCheck->hostCheck(config('values.host'));

        $datas = [];

        foreach ($subChapters as $item) {
            $learning = Learning::with('theories')->find($item->learning_id);
            $data['id'] = $item->id;
            $data['order'] = $item->order;

            if (in_array($chapter->type,[1,2])) {
                if (isset($item->tags)) {
                    $data['tags'] = $item->tags;
                } else {
                    $data['tags'] = [];
                }
            } else {
                if (isset($learning))
                    $data['tags'] = $this->buildTagArray($learning->theories);
                else
                    $data['tags'] = [];
            }

            $urlPath = 'sub-chapter/image/';
            if ($chapter->type == 1) {
                $urlPath = 'quiz/';
            }

            if ($chapter->type == 1) {
                $image = Quiz::find($item->quiz_image);
                $data['thumbnail_unlocked'] = $apiFunctions->imgNullChck($image, $apiFunctions->sizesConvert($size), 'image_unlocked', $urlPath . $host . "/", true);
            } else {
                $data['thumbnail_unlocked'] = $apiFunctions->imgNullChck($item, $apiFunctions->sizesConvert($size), 'image_unlocked', $urlPath . $host . "/");
            }
            if ($chapter->type == 1) {
                $image = Quiz::find($item->quiz_image);
                $data['thumbnail_locked'] = $apiFunctions->imgNullChck($image, $apiFunctions->sizesConvert($size), 'image_locked', $urlPath . $host . "/", true);
            } else {
                $data['thumbnail_locked'] = $apiFunctions->imgNullChck($item, $apiFunctions->sizesConvert($size), 'image_locked', $urlPath . $host . "/");
            }
            $data['background_original'] = null;
            $data['background_filtered'] = null;
//            if (isset($item->transform)) {
//                $data['transform'] = json_decode($item->transform);
//            }else{
//                $data['transform'] = null;
//            }
            $datas [] = $data;
        }
        if ($rec) {
            return $datas;
        }
        return response(json_encode($datas, JSON_UNESCAPED_UNICODE));
    }

    private function buildTagArray($theories)
    {
        $tagsArray = [];
        if (count($theories) == 0){
            $tagsArray [] = 'practice';
        }else {
            foreach ($theories as $item) {
                foreach ($item->tags as $tag)
                    $tagsArray [] = $tag;
            }
        }
        return $tagsArray;
    }
}
