<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\Api;
use App\Http\Controllers\Services\Images;
use App\Http\Controllers\Services\Info;
use App\Http\Controllers\Services\Summary;
use App\Http\Controllers\Services\Theory;
use App\Models\Learning;
use App\Models\SubChapter;
use Illuminate\Http\Request;

class TheoryController extends Controller
{

    public function getTheories(Request $request)
    {
        $theoryNeeded = false;
        $summaryNeeded = false;
        $theory = null;
        $summaryData = null;

        $apiFunctions = new Api();

        $web = 0;
        if (array_key_exists('web',$request->all())){
            $web = $request->web;
        }

        if (array_key_exists('lcid', $request->all())) {
            $lcid = $request->lcid;
            if ($apiFunctions->lcidExistingCheck($lcid,$web) <> $lcid) {
                return response('bad lcid', 400);
            }
        } else {
            $lcid = "none";
        }

        if (array_key_exists('icons_scale', $request->all())) {
            $image = new Images();
            $size = $image->iconScaleCheck($request->icons_scale);
        } else {
            $size = "S";
        }

        if (array_key_exists('sub-chapter-id', $request->all())) {
            $subChapterId = 'sub-chapter-id';
            $ids = explode(',', $request->$subChapterId);
        } else {
            return response('bad sub-chapter-id', 400);
        }

        if (array_key_exists('content', $request->all())) {
            $content = 'content';
            $content = explode(',', $request->$content);
            if (isset($content)) {
                foreach ($content as $item) {
                    if ($item == 'summary'){
                        $summaryNeeded = true;
                    }
                    if ($item == 'theory'){
                        $theoryNeeded = true;
                    }
                }
            }
        } else {
            return response('bad content', 400);
        }
        $theoryFunctions = new Theory();
        $datas = null;

        foreach ($ids as $id) {
            $data = null;

            $scene = SubChapter::find($id);
            if ($theoryNeeded) {
                $learning = Learning::with('theories')->find($scene->learning_id);

                if (isset($learning->theories)) {
                    $theory = $theoryFunctions->buildTheoryData($learning->theories, $size, $lcid);
                }
                if (count($theory) == 0) {
                    $theory = null;
                }
            }
            if ($summaryNeeded){
                $info = new Info();

                $summaryFunctions = new Summary();
                $summary = $summaryFunctions->getSummaryRecursive(0, $lcid, $scene);
                $dial = SubChapter::where('sub_chapter_id', $scene->id)->first();
                if (isset($summary) && !empty($summary)) {
                    $summaryData = [
                        'id' => 0,
                        'nextId' => null,
                        'text' => $summary,
                        'background' => $info->backgroundGet($dial, $size),
                        'characters' => [],
                    ];
                } else {
                    $summaryData = null;
                }

            }
            $data = [
                "scene_id" => $id,
                "scene_content" => [
                    "theory" => $theory,
                    "summary" => $summaryData
                ]
            ];
            $datas[] = $data;
        }
        return json_encode($datas, JSON_INVALID_UTF8_SUBSTITUTE);

    }
}
