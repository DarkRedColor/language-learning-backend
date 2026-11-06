<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\Api;
use App\Http\Controllers\Services\HostChecker;
use App\Http\Controllers\Services\Images;
use App\Http\Controllers\Services\Version;
use App\Models\Background;
use App\Models\Chapter;
use App\Models\Episode;
use App\Models\File;
use App\Models\Introduction;
use App\Models\Learning;
use App\Models\Quiz;
use App\Models\Season;
use App\Models\SubChapter;
use App\Models\Text;
use App\Models\Theory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class V2AdventuresController extends Controller
{
    private $versions = [
        'v2' => true,
        'v3' => true
    ];

    public function allSeasonsV2(Request $request)
    {
        $version = 'v2';
        $lcid = 'en';
//            $size = 'L';
        $ids = null;
//            $seasons = null;

        if (array_key_exists('lcid', $request->all())) {
            $lcid = $request->lcid;
        }

//            if (array_key_exists('icons_scale', $request->all())) {
//                $size = $request->icons_scale;
//            }
        $web = 0;
        if (array_key_exists('web', $request->all())) {
            $web = $request->web;
        }
        $apiFunctions = new Api();

        if ($apiFunctions->lcidExistingCheck($lcid, $web) <> $lcid) {
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
        } else {
            $seasons = Season::whereNotNull('order')
                ->where('is_displayed_to_user', 1)
                ->orderBy('order')
                ->get();
        }

        $datas = [];

        if (isset($seasons)) {
            foreach ($seasons as $key => $season) {
                $data['id'] = $season->id;
                $data['description'] = $apiFunctions->localiseDataNullCheck($season, $lcid, 'name_', true);
                $data['order'] = $key;
                if (isset($ids)) {
                    $request['id_season'] = $season->id;
                    $data['episodes'] = $this->getAllSeasonsEpisodes($request, true, $version);
                }
                $datas [] = $data;
            }
        }

        return response(json_encode($datas, JSON_UNESCAPED_UNICODE));

    }

    public function allSeasonsV3(Request $request)
    {
        $version = 'v3';
        $lcid = 'en';
//            $size = 'L';
        $ids = null;
//            $seasons = null;

        if (array_key_exists('lcid', $request->all())) {
            $lcid = $request->lcid;
        }
        $web = 0;
        if (array_key_exists('web', $request->all())) {
            $web = $request->web;
        }
//            if (array_key_exists('icons_scale', $request->all())) {
//                $size = $request->icons_scale;
//            }

        $apiFunctions = new Api();

        if ($apiFunctions->lcidExistingCheck($lcid, $web) <> $lcid) {
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
        } else {
            $seasons = Season::whereNotNull('order')
                ->where('is_displayed_to_user', 1)
                ->orderBy('order')
                ->get();
        }

        $datas = [];


        if (isset($seasons)) {
            foreach ($seasons as $key => $season) {
                $data['id'] = $season->id;
                $data['description'] = $apiFunctions->localiseDataNullCheck($season, $lcid, 'name_', true);
                $data['order'] = $key;
                if (isset($ids)) {
                    $request['id_season'] = $season->id;
                    $data['episodes'] = $this->getAllSeasonsEpisodes($request, true, $version);
                }
                $datas [] = $data;
            }
        }

        return response(json_encode($datas, JSON_UNESCAPED_UNICODE));

    }

    public function getAllSeasonsEpisodes(Request $request, $rec = false, $version = 'v2')
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
        $size = 'L';

        if (array_key_exists('lcid', $request->all())) {
            $lcid = $request->lcid;
        }

        $web = 0;
        if (array_key_exists('web', $request->all())) {
            $web = $request->web;
        }
        if (array_key_exists('icons_scale', $request->all())) {
            $size = $request->icons_scale;
        }

        $apiFunctions = new Api();

        if ($apiFunctions->lcidExistingCheck($lcid, $web) <> $lcid) {
            return response('bad lcid', 400);
        }

        $episodes = Episode::where('season_id', $id)
            ->whereNotNull('order')
            ->where('is_displayed_to_user', 1)
            ->orderBy('order')
            ->get();

        if (count($episodes) === 0) {
            return [];
        }


        $datas = [];
        foreach ($episodes as $episode) {

            $data['id'] = $episode->id;
            $data['description'] = $apiFunctions->localiseDataNullCheck($episode, $lcid, 'name_', true);
            $data['order'] = $episode->order;
            $data['image'] = null;
            if (isset($episode->background_episode_id)) {
                $background = Background::find($episode->background_episode_id) ?? null;
                if (isset($background)) {
                    if ($version == 'v3') {
                        $imagesFunctions = new Images();
                        $timeStamps = $background->updated_at->getTimestamp();
                        $mediaUrl = Storage::disk('gcs')->url("backgrounds/$background->name" . "_$size.webp");
                        $data['image'] = $imagesFunctions->apiMediaModel($mediaUrl, $timeStamps, 0);
                    } else {
                        $data['image'] = Storage::disk('gcs')->url("backgrounds/$background->name" . "_$size.webp");
                    }
                }
            }
            if (isset($episode->transform)) {
                $data['transform'] = json_decode($episode->transform);
            } else {
                $data['transform'] = null;
            }


//            here will need size
//            if (!$data['image'] == null) {
//                $data['image'] = $episode['image'] . $this->size($size);
//            }
            if ($rec) {
                $request['id_episode'] = $episode->id;
                $data['chapters'] = $this->getAllEpisodesChapters($request, true, $version);
            }

            $datas [] = $data;
        }
        if ($rec) {
            return $datas;
        }
        return response(json_encode($datas, JSON_UNESCAPED_UNICODE));
    }

    public function getAllEpisodesChapters(Request $request, $rec = false, $version = 'v2')
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
        $web = 0;
        if (array_key_exists('web', $request->all())) {
            $web = $request->web;
        }
        if (array_key_exists('icons_scale', $request->all())) {
            $size = $request->icons_scale;
        }

        $apiFunctions = new Api();

        if ($apiFunctions->lcidExistingCheck($lcid, $web) <> $lcid) {
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
            $data = null;
            $data['id'] = $chapter->id;
            $data['description'] = $apiFunctions->localiseDataNullCheck($chapter, $lcid, 'name_', true);
            $data['order'] = $chapter->order;
            $data['type'] = $chapter->type;
            if ($version == "v3") {
                $imagesFunctions = new Images();
                $timeStamps = $chapter->updated_at->getTimestamp();
                $mediaUrl = $apiFunctions->imgNullChck($chapter, $apiFunctions->sizesConvert($size), 'image_unlocked', 'chapter/image' );
                $data['thumbnail'] = $imagesFunctions->apiMediaModel($mediaUrl, $timeStamps, 0);
                $introduction = Introduction::find($chapter->introduction_id);
                if ($introduction <> null) {
                    $data['brief'] = $apiFunctions->localiseDataNullCheck($introduction, $lcid, 'name_', true);
                } else {
                    $data['brief'] = null;
                }
            } else {
                $data['thumbnail'] = $apiFunctions->imgNullChck($chapter, $apiFunctions->sizesConvert($size), 'image_unlocked', 'chapter/image');
            }
            if ($rec) {
                $request['id_chapter'] = $chapter->id;
                if (isset($chapter->transform)) {
                    $data['transform'] = json_decode($chapter->transform);
                } else {
                    $data['transform'] = null;
                }
                if ($chapter->type == 0) {
                    $data['default_scenes'] = $this->getAllChaptersSubChapters($request, $chapter->type, true, $version);
                } elseif ($chapter->type == 1) {
                    $data['quiz_scenes'] = $this->getAllChaptersSubChapters($request, $chapter->type, true, $version);
                } elseif ($chapter->type == 2) {
                    $data['intro_scenes'] = $this->getAllChaptersSubChapters($request, $chapter->type, true, $version);
                }
            }
            $datas [] = $data;
        }
        if ($rec) {
            return $datas;
        }
        return response(json_encode($datas, JSON_UNESCAPED_UNICODE));
    }

    public function getAllChaptersSubChapters(Request $request, $chapterType, $rec = false, $version = 'v2')
    {
        if (array_key_exists('id_chapter', $request->all())) {
            $id = (int)$request->id_chapter;
            if ($id == 0) {
                return response('bad id_chapter', 400);
            }
        } else {
            return response('bad id_chapter', 400);
        }
        $lcid = "en";
        if (array_key_exists('lcid', $request->all())) {
            $lcid = $request->lcid;
        }
        $web = 0;
        if (array_key_exists('web', $request->all())) {
            $web = $request->web;
        }

        $apiFunctions = new Api();

        if ($apiFunctions->lcidExistingCheck($lcid, $web) <> $lcid) {
            return response('bad lcid', 400);
        }

        $subChapters = SubChapter::where('chapter_id', $id)
            ->whereNotNull('order')
            ->Where('is_displayed_to_user', true)
            ->where('type', 0)
            ->orderBy('order')
            ->get();

        if (count($subChapters) === 0) {

            return [];

        }

        $datas = [];

        foreach ($subChapters as $item) {
            $learningId = $item->learning_id;

            if (isset($learningId)) {
                $learning = Learning::with('theories')->find($learningId);
                if (isset($learning->theories) && count($learning->theories) <> 0) {
                    $theory = $learning->theories->first();
                    if (isset($theory)) {
                        if (isset($theory->description_en)) {
                            $localisedField = 'description_' . $lcid;
                            if (isset($theory->$localisedField)) {
                                $theoryTitle = $theory->$localisedField;
                            } else {
                                $theoryTitle = $theory->description_en;
                            }
                        } else {
                            $localisedColumn = "name_" . $lcid;
                            $text = Text::where('name_en', "Words")->first();
                            if (isset($text)) {
                                $theoryTitle = $text->$localisedColumn;
                            } else {
                                $theoryTitle = "Words";
                            }
                        }
                    }
                } else {
                    $theoryTitle = null;
                }
                $typeTypeTransform = [
                    '0' => 3,
                    '1' => 2,
                    '2' => 1
                ];
                $sceneType = 0;
                if (isset($learning->practices) && count($learning->practices) <> 0){
                    $practice = $learning->practices->first();
                    if (isset($practice)) {
                        $file = File::find($practice->file_id);
                        if (isset($file)){
                            $sceneType = $typeTypeTransform[$file->type];

                        }
                    }
                }
            }
            $data['id'] = $item->id;
            $data['order'] = $item->order;
            $data['scene_theory_title'] = $theoryTitle ?? null;
            $data['scene_type'] = $sceneType ?? 0;
            if (isset($item->transform)) {
                $data['transform'] = json_decode($item->transform);
            } else {
                $data['transform'] = null;
            }
            if ($chapterType == 1) {
                $hostCheck = new HostChecker();
                $size = "L";
                if (array_key_exists('icons_scale', $request->all())) {
                    $size = $request->icons_scale;
                }
                $host = $hostCheck->hostCheck(config('values.host'));
                $image = Quiz::find($item->quiz_image);
                if ($version == 'v3') {
                    $imagesFunctions = new Images();
                    $timeStamps = $image->updated_at->getTimestamp();
                    $mediaUrl = $apiFunctions->imgNullChck($image, $apiFunctions->sizesConvert($size), 'image_unlocked', "sub-chapter", true);
                    $data['icon'] = $imagesFunctions->apiMediaModel($mediaUrl, $timeStamps, 0);
                } else {
                    $data['icon'] = $apiFunctions->imgNullChck($image, $apiFunctions->sizesConvert($size), 'image_unlocked', "sub-chapter" . $host . "/", true);
                }
                if (isset($item->tags)) {
                    $data['title'] = $item->tags[0];
                } else {
                    $data['title'] = null;
                }
            }
            $datas [] = $data;
        }
        if ($rec) {
            return $datas;
        }
        return response(json_encode($datas, JSON_UNESCAPED_UNICODE));
    }
}
