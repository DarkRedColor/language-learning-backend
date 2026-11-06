<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\Background;
use App\Models\Chapter;
use App\Models\Episode;
use App\Models\Intro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Info extends Controller
{
    private function textDataBuild($scene, $lcid, $chapter = null)
    {
        if (!isset($chapter)) {
            $chapter = Chapter::find($scene->chapter_id);
        }
        $text = new Text();
        $introText = \App\Models\Text::where('name_en', 'Intro')->first();
        $chapterText = \App\Models\Text::where('name_en', 'Chapter')->first();
        $sceneText = \App\Models\Text::where('name_en', 'Scene')->first();
        $localisateNameOfChapter = 'no chapter';
        $chapterOrder = 0;
        $sceneOrder = $scene->order + 1;

        $chapterWordLocalisate = 'Chapter';
        if ($chapter->type == 2) {
            if (isset($introText)) {
                $chapterWordLocalisate = $text->onlyLocalisationGet($lcid, 'name', $introText);
            }
        } else {
            if (isset($chapterText)) {
                $chapterWordLocalisate = $text->onlyLocalisationGet($lcid, 'name', $chapterText);
            }
        }

        $sceneWordLocalisate = 'Scene';
        if (isset($sceneText)) {
            $sceneWordLocalisate = $text->onlyLocalisationGet($lcid, 'name', $sceneText);
        }

        if (isset($chapter)) {
            $localisateNameOfChapter = $text->onlyLocalisationGet($lcid, 'name', $chapter);
            $allEpisodeChaptersType = Chapter::where('episode_id', $chapter->episode_id)->pluck('type')->all();
            $chapterOrder = $chapter->order + 1;
            if (in_array(2, $allEpisodeChaptersType)) {
                $chapterOrder = $chapter->order;
            }
        }

        $res = "$chapterWordLocalisate $chapterOrder: $localisateNameOfChapter $sceneWordLocalisate $sceneOrder";
        if ($chapter->type == 2) {
            $res = "$chapterWordLocalisate: $localisateNameOfChapter $sceneWordLocalisate $sceneOrder";
        }

        return $res;
    }

    public function backgroundGet($dialogue, $size)
    {
        $res = null;
        $firstDialogueFrame = null;
        if (isset($dialogue)) {
            $firstDialogueFrame = Intro::Where('sub_chapter_id', $dialogue->id)
                ->where('parent_id', null)
                ->where('order', 0)->first();
        }
        if (isset($firstDialogueFrame)) {

            $background = Background::find($firstDialogueFrame->background_id) ?? null;

            if (isset($background)) {
                $res = Storage::disk('gcs')->url("backgrounds/$background->name" . "_$size.webp");
            }

            return $res;
        }
        return null;
    }

    public function infoDataBuild($scene, $dialogue, $lcid, $size, $v2 = false, $chapter = null, $episode = null, $background = null)
    {
        $infoData = [];
//        if ($scene->type == 0) {
//            $infoData = [
//                'id' => 0,
//                'nextId' => null,
//                'text' => $this->textDataBuild($scene, $lcid),
//                'background' => $this->backgroundGet($dialogue, $size),
//                'characters' => [],
//            ];
//        }
        if (in_array($scene->type, [1, 0, 2])) {
            if (!isset($chapter))
                $chapter = Chapter::find($scene->chapter_id);
            $episodeBackground = null;

            if (isset($chapter)) {
                if (!isset($episode))
                    $episode = Episode::find($chapter->episode_id);
                if (isset($episode)) {
                    if (isset($episode->background_id)) {
                        if (!isset($background))
                            $background = Background::find($episode->background_id);
                        $imagesFunctions = new Images();
                        $timeStamps = $background->updated_at->getTimestamp();
                        $mediaUrl = Storage::disk('gcs')->url("backgrounds/$background->name" . "_$size.webp");
                        if ($v2) {
                            $episodeBackground = $imagesFunctions->apiMediaModel($mediaUrl, $timeStamps, 0);
                        } else {
                            $episodeBackground = $mediaUrl;
                        }
                    }

                }
            }
            $infoData = [
                'id' => 0,
                'nextId' => null,
                'text' => $this->textDataBuild($scene, $lcid, $chapter),
                'background' => $episodeBackground,
                'characters' => [],
            ];
        }
        return $infoData;

    }
}
