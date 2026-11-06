<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Episode;
use App\Models\Season;
use App\Models\SubChapter;
use Illuminate\Http\Request;

class Summary extends Controller
{
    public $response;

    public function getSummaryRecursive($level, $lcid, $precedentEntity)
    {
//        $response = null;
        $levelsArray = [
            'chapter_id',
            'episode_id',
            'season_id'
        ];
        $modelsArray = [
            new SubChapter(),
            new Chapter(),
            new Episode(),
            new Season()
        ];

        $entityId = 4;
        $allElementsOfLevel = $modelsArray[$level]->all();

        if (array_key_exists($level, $levelsArray)) {
            $entityId = $levelsArray[$level];
            $allElementsOfLevel = $modelsArray[$level]->where($levelsArray[$level], $precedentEntity->$entityId)->get();
        }
        //        $allElementsOfLevel = $modelsArray[$level]->where($levelsArray[$level], $precedentEntity->$entityId)->get();

        if ($precedentEntity->order != 0) {
            $precedentEntityElement = $allElementsOfLevel
                ->whereNotNull('order')
                ->where('order', $precedentEntity->order - 1)
                ->first();
            if (isset($precedentEntityElement)) {
                switch ($level) {
                    case (1):
                        $precedentEntityElement = $this->getLastScene($precedentEntityElement);
                        break;
                    case (2):
                        $precedentEntityElement = $this->getPrecedentChapterLastScene($precedentEntityElement);
                        break;
                    case (3):
                        $precedentEntityElement = $this->getPrecedentEpisodePrecedentChapterLastScene($precedentEntityElement);
                        break;
                    default:
                        break;
                }
                if (isset($precedentEntityElement)) {
                    $this->response = $this->getSummaryFromDialogue($precedentEntityElement, $lcid);
                }
            }
        } else {
            if (array_key_exists($level + 1, $modelsArray)) {
                $model = $modelsArray[$level + 1]->find($precedentEntity[$entityId]);
                $this->getSummaryRecursive($level + 1, $lcid, $model);
            }
        }
        return $this->response;
    }

    private function getLastScene($chapter)
    {
        $lastScene = SubChapter::where('chapter_id', $chapter->id)
            ->latest('order')
            ->first();
        return $lastScene ?? null;
    }

    private function getPrecedentChapterLastScene($episode)
    {
        $lastChapter = Chapter::where('episode_id', $episode->id)
            ->latest("order")
            ->first();
        $res = null;

        if (isset($lastChapter)) {
            $res = $this->getLastScene($lastChapter);
        }
        return $res;
    }

    private function getPrecedentEpisodePrecedentChapterLastScene($season)
    {
        $lastEpisode = Episode::where('season_id', $season->id)
            ->latest('order')
            ->first();
        $res = null;
        if (isset($lastChapter)) {
            $res = $this->getPrecedentChapterLastScene($lastEpisode);
        }
        return $res;
    }

    private function getSummaryFromDialogue($scene, $lcid)
    {
        $dialogue = SubChapter::where('sub_chapter_id', $scene->id)->first();
        $description = null;
        if (isset($dialogue)) {
            $description[0] = $dialogue->description_en;
        }

        $field = 'description_' . $lcid;

        if (isset($dialogue->$field)) {
            $description[1] = $dialogue->$field;
        }
        return $description;
    }
}
