<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\Api;
use App\Http\Controllers\Services\HostChecker;
use App\Http\Controllers\Services\Images;
use App\Http\Controllers\Services\IntroductionWelcome;
use App\Models\Background;
use App\Models\Chapter;
use App\Models\Episode;
use App\Models\Introduction;
use App\Models\Learning;
use App\Models\Motivation;
use App\Models\Quiz;
use App\Models\Season;
use App\Models\SubChapter;
use App\Models\Text;
use App\Models\Welcome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FinalQuizController extends Controller
{
    public function finalQuizGet(Request $request)
    {
        $lcid = 'en';
        if (array_key_exists('lcid', $request->all())) {
            $lcid = $request->lcid;
        }
        $apiFunctions = new Api();

        if ($apiFunctions->lcidExistingCheck($lcid) <> $lcid) {
            return response('bad lcid', 400);
        }
        $size = 'L';
        if (array_key_exists('icons_scale', $request->all())) {
            $size = $request->icons_scale;
        }
        if (array_key_exists('season_id', $request->all())) {
            $season_id = $request->season_id;
        } else {
            return response('inexistent season', 400);
        }

        $season = Season::find($season_id);
        if (!isset($season)) {
            return response('inexistent season', 400);
        } else {
            $episode = Episode::where('type', 1)
                ->where('season_id', $season_id)
                ->first();
            if (!isset($episode)) {
                return response('no content', 400);
            } else {
                $imagesFunctions = new Images();
                $background = Background::find($episode->background_id) ?? null;
                $timeStamps = $background->updated_at->getTimestamp();
                $mediaUrl = Storage::disk('gcs')->url("backgrounds/$background->name"."_$size.webp");

                $chapter = Chapter::where('episode_id', $episode->id)->first();
                $introductionWelcomeFunctions = new IntroductionWelcome();
                $introduction = new Introduction();
                $welcome = new Welcome();
                $motivations = new Motivation();
                $type = $chapter->type;
                $scenes = SubChapter::where('chapter_id', $chapter->id)->get();
                $introductionData =null;
                $motivationData =[];
                $welcomeData =[];
                foreach ($scenes as $scene) {
                    $introductionData['frames'] = $introductionWelcomeFunctions->DataBuild($scene, $lcid, $size, $introduction, $type, 1, true, $chapter, $episode, $background)[0] ?? null;
                    $motivationData['frames'] = $introductionWelcomeFunctions->DataBuild($scene, $lcid, $size, $motivations, $type, 2, true, $chapter, $episode, $background);
                    $welcomeData['frames'] = $introductionWelcomeFunctions->DataBuild($scene, $lcid, $size, $welcome, $type, 4, true, $chapter, $episode, $background);
                }

                $datas = [
                    'id' => $episode->id,
                    'description' => $episode->name,
                    'image' => $imagesFunctions->apiMediaModel($mediaUrl, $timeStamps, 0),
                    'introduction' => $introductionData,
                    'motivation' => $motivationData,
                    'welcome' => $welcomeData,
                    'scenes' => $this->scenesDataBuild($scenes, $lcid, $type, $size),
                ];
            }
        }
        return json_encode($datas, JSON_INVALID_UTF8_SUBSTITUTE);
    }

    private function scenesDataBuild($scenes, $lcid, $chapterType, $size)
    {
        $datas = null;
        foreach ($scenes as $item) {
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
            }
            $data['id'] = $item->id;
            $data['order'] = $item->order;
            $data['scene_theory_title'] = $theoryTitle ?? null;

                $urlPath = 'quiz';
                $image = Quiz::find($item->quiz_image);
                $imagesFunctions = new Images();
                $timeStamps = $image->updated_at->getTimestamp();
                $apiFunctions = new Api();
                $mediaUrl = $apiFunctions->imgNullChck($image, $apiFunctions->sizesConvert($size), 'image_unlocked', $urlPath  , true);
                $data['icon'] = $imagesFunctions->apiMediaModel($mediaUrl, $timeStamps, 0);

                if (isset($item->tags)) {
                    $data['title'] = $item->tags[0];
                } else {
                    $data['title'] = null;
                }

            $datas [] = $data;
        }

        return $datas;

    }
}
