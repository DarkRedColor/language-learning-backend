<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\Background;
use App\Models\Chapter;
use App\Models\Emotion;
use App\Models\Episode;
use App\Models\Learning;
use App\Models\Personage;
use Illuminate\Http\Request;
use function React\Promise\all;

class IntroductionWelcome extends Controller
{
    public function DataBuild($scene, $lcid, $size, $model, $sceneType, $welcome = 0, $v2 = false, $chapter = null, $episode = null, $background = null, $learning = null)
    {
        $textFunctions = new Text();
        $backgroundFunctions = new Backgrounds();
        $image = new Images();
        switch ($welcome) {
            case (2):
                $allData = $model->all();
                break;
            case (4):
            case (3):
                $allData = $model->where('type', 2)->get();
                break;
            default:
                $allData = $model->where('type', 0)->get();
                break;
        }

        if ($sceneType == 1 && $welcome <> 2 && $welcome <> 3) {
            $allData = $model->where('type', 1)->get();
//            $allData =$model->where('type', 2)->get();
//            $allData = $allData->merge($model->where('type', 1)->get());
        }
        $dataBuilded = [];
        if (!isset($chapter))
            $chapter = Chapter::find($scene->chapter_id);
        if (isset($chapter)) {
            if (!isset($episode))
                $episode = Episode::find($chapter->episode_id);
            if (isset($episode)) {
                if ($v2) {
                    $background = $backgroundFunctions->backgroundV2Get($episode, $size, $background);
                } else {
                    $background = $backgroundFunctions->backgroundGet($episode, $size);
                }
            }
        }

        $theoryName = null;
        if ($welcome == 1) {
            if (!isset($learning)) {
                $learningWithTheories = $learning = Learning::with('theories')->find($scene->learning_id);
            } else {
                $learningWithTheories = $learning;
            }
            if (isset($learningWithTheories->theories)) {
                $theories = $learningWithTheories->theories;
                if (isset($theories[0])) {
                    if ($theories[0]->type != 0) {
                        $theoryName = $textFunctions->onlyLocalisationGet($lcid, 'description', $theories[0]);
                    } else {
                        $localisedColumn = "name_" . $lcid;
                        $theory = \App\Models\Text::where('name_en', "Words")->first();
                        if (isset($theory)) {
                            $theoryName = $theory->$localisedColumn;
                        } else {
                            $theoryName = "Words";
                        }
                    }
                }
            }
        }
        if (isset($chapter->introduction_id) && $allData->find($chapter->introduction_id) !== null) {
            $text = $textFunctions->onlyLocalisationGet($lcid, 'name', $allData->find($chapter->introduction_id));
            if (isset($chapter->emotion_id)) {
                $emotion = Emotion::find($chapter->emotion_id);
                $characterData = [
                    ['id' => $chapter->personage_id,
                        'position' => 0,
                        'active' => 1,
                        'emotion_id' => $image->emotionsCatigorise($emotion->name)
                    ]
                ];
            } else {
                $characterData = [
                    ['id' => 4,
                        'position' => 0,
                        'active' => 1,
                        'emotion_id' => 1]
                ];
            }
            $dataBuilded [] = [
                'id' => $chapter->introduction_id,
                'nextId' => null,
                'text' => $text,
                'background' => $background,
                'characters' => $characterData
            ];
        } elseif ($welcome == 3) {
            $dataBuilded [] = null;
        } else {
            foreach ($allData as $item) {
                $text = $textFunctions->onlyLocalisationGet($lcid, 'name', $item);
                if ($welcome == 1) {
                    $text = $textFunctions->onlyLocalisationGet($lcid, 'name', $item) . ' ' . $theoryName;
                }
                $dataBuilded [] = [
                    'id' => $item->id,
                    'nextId' => null,
                    'text' => $text,
                    'background' => $background,
                    'characters' => [
                        ['id' => 4,
                            'position' => 0,
                            'active' => 1,
                            'emotion_id' => 1]
                    ],
                ];
            }
        }
        if ($welcome == 3) {
            [$dataBuildedTemp,$learning] = $this->DataBuild($scene, $lcid, $size, $model, $sceneType, 1, true);
            $dataBuilded = array_merge($dataBuilded, $dataBuildedTemp);
            return [$dataBuilded,$learning];
        }
        if ($welcome == 1){
            return [$dataBuilded,$learning];
        }
        return $dataBuilded;
    }
}
