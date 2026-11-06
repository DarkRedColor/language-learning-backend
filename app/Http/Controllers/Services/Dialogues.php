<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\api\IntroApiController;
use App\Http\Controllers\Controller;
use App\Models\Animation;
use App\Models\AnimationEmotion;
use App\Models\Background;
use App\Models\Emotion;
use App\Models\Intro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use function Ramsey\Uuid\v2;

class Dialogues extends Controller
{
    private $emotions = [];
    private $dialogues = [];

    private function choicesDataBuild($choice, $dialogues, $localisedField)
    {
        $choices = [];
        if (isset($choice->next_id)) {
            foreach ($choice->next_id as $item) {
                $choiceChild = $dialogues->find($item[0]);
                if (isset($choiceChild)) {
                    $choices [] = [
                        'next_id' => $choiceChild->next_id[0][0],
                        'text' => $choiceChild->$localisedField ?? $choiceChild->text_en,
                        'english_text' => $choiceChild->text_en
                    ];
                }
            }
        }
        return $choices;

    }

    public function dialogueDefaultFramesDataBuild($dialogueId, $size, $lcid, $v2 = false, $background = null, $chapter = null)
    {
        $dialogues = Intro::where('sub_chapter_id', $dialogueId)
            ->where('content_type', '!=', 3)->get();
        $defaultFramesData = [];
        $image = new Images();
        foreach ($dialogues as $item) {
            if (isset($item->personage_id)) {
                if (array_key_exists($item->emotion_id, $this->emotions)) {
                    $emotion = $this->emotions[$item->emotion_id];
                } else {
                    $emotion = $this->emotions[$item->emotion_id] = Emotion::find($item->emotion_id);
                }
                if (isset($emotion)) {
                    if (isset($chapter)) {
                        if ($item->personage_id == $chapter->personage_id) {
                            $characters = [
                                [
                                    'id' => $item->personage_id,
                                    'position' => $item->position,
                                    'active' => 1,
//                            $item->current,
                                    'emotion_id' => $image->emotionsCatigorise($emotion->name),
                                    'hide_name' => !(($item->hide_name == 0)),
                                    'playable' => 1
                                ]
                            ];
                        } else {
                            $characters = [
                                [
                                    'id' => $item->personage_id,
                                    'position' => $item->position,
                                    'active' => 1,
//                            $item->current,
                                    'emotion_id' => $image->emotionsCatigorise($emotion->name),
                                    'hide_name' => !(($item->hide_name == 0)),
                                    'playable' => 0
                                ]
                            ];
                        }
                    } else
                        $characters = [
                            [
                                'id' => $item->personage_id,
                                'position' => $item->position,
                                'active' => 1,
//                            $item->current,
                                'emotion_id' => $image->emotionsCatigorise($emotion->name),
                                'hide_name' => !(($item->hide_name == 0))
                            ]
                        ];
                } else {
                    $characters = [
                        [
                            'id' => $item->personage_id,
                            'position' => $item->position,
                            'active' => 1,
//                            $item->current,
                            'emotion_id' => null,
                            'hide_name' => !(($item->hide_name == 0))
                        ]
                    ];
                }
            } else
                $characters = [];
            if (isset($item->next_id[0][0])) {
                $nextId = (int)$item->next_id[0][0];
            } else {
                $oldDialogueFunctions = new IntroApiController();
                [$nextId, $this->dialogues] = $oldDialogueFunctions->nextGet($item, $this->dialogues);
            }
            $localisedField = "text_en";
            if ($lcid <> "en") {
                $localisedField = "text_$lcid";
            }
            $riveData = null;
            if ($item->animation_id <> null) {
                $fileAnimation = AnimationEmotion::find($item->animation_id);
                if (isset($fileAnimation)) {
                    $fileAnimationName = $fileAnimation->name;
                    $file = Animation::find($fileAnimation->animation_id);
                    $fileName = $file->name;
                    $imagesFunctions = new Images();
                    $timeStamps = $file->updated_at->getTimestamp();
                    $mediaUrl = Storage::disk('gcs')->url("animations/$fileName");
                    if ($v2) {
                        $mediaUrl = $imagesFunctions->apiMediaModel($mediaUrl, $timeStamps, 3);
                    }
                    $riveData = [
                        'rive_file' => $mediaUrl,
                        'artboard_name' => $fileAnimationName
                    ];
                }
            }
            $audioData = null;
            if ($item->voice <> 0) {
                $imagesFunctions = new Images();
                $fileName = $item->id . '.mp3';
                $timeStamps = $item->updated_at->getTimestamp();
                $audioUrl = Storage::disk('gcs')->url("dialogue/$fileName");
                $audioData = $imagesFunctions->apiMediaModel($audioUrl, $timeStamps, 1);
            }

            if ($item->content_type == 0) {
                $defaultFramesData[] = [
                    'id' => $item->id,
                    'nextId' => $nextId,
                    'text' => $item->$localisedField ?? $item->text_en,
                    'english_text' => $item->text_en,
                    'background' => $this->backgroundGet($item, $size, $v2, $background),
                    'characters' => $characters,
                    'rive_data' => $riveData,
                    'audio_data' => $audioData ?? null
                ];
            } else {
                $defaultFramesData[] = [
                    'id' => $item->id,
                    'nextId' => $nextId,
                    'text' => $item->$localisedField ?? $item->text_en,
                    'english_text' => $item->text_en,
                    'background' => $this->backgroundGet($item, $size, $v2, $background),
                    'characters' => $characters,
                    'rive_data' => $riveData,
                    'audio_data' => null
                ];
            }
        }

        return $defaultFramesData;
    }

    public function dialogueChoiceFramesDataBuild($dialogueId, $size, $lcid, $v2 = false, $background = null)
    {
        $dialogues = Intro::where('sub_chapter_id', $dialogueId)
            ->where('content_type', 3)->get();
        $image = new Images();
        $localisedField = "text_en";
        if ($lcid <> "en") {
            $localisedField = "text_$lcid";
        }
        $choiceFramesData = [];
        foreach ($dialogues as $item) {
            if (!isset($item->text_en)) {
                if (array_key_exists($item->emotion_id, $this->emotions)) {
                    $emotion = $this->emotions[$item->emotion_id];
                } else {
                    $emotion = $this->emotions[$item->emotion_id] = Emotion::find($item->emotion_id);
                }
                if (isset($emotion))
                    $choiceFramesData[] = [
                        'id' => $item->id,
                        'background' => $this->backgroundGet($item, $size, $v2, $background),
                        'characters' => [[
                            'id' => $item->personage_id,
                            'position' => $item->position,
                            'active' => 1,
                            'emotion_id' => $image->emotionsCatigorise($emotion->name),
                            'hide_name' => !(($item->hide_name == 0))
                        ]],
                        'choices' => $this->choicesDataBuild($item, $dialogues, $localisedField),
                    ];
                else
                    $choiceFramesData[] = [
                        'id' => $item->id,
                        'background' => $this->backgroundGet($item, $size, $v2, $background),
                        'characters' => [[
                            'id' => $item->personage_id,
                            'position' => $item->position,
                            'active' => 1,
                            'emotion_id' => null,
                            'hide_name' => !(($item->hide_name == 0))
                        ]],
                        'choices' => $this->choicesDataBuild($item, $dialogues, $localisedField),
                    ];
            }
        }
        return $choiceFramesData;
    }

    public function getTheFirstOneId($dialogueId)
    {
        $dialogue = Intro::where('sub_chapter_id', $dialogueId)
            ->where('parent_id', null)
            ->where('order', 0)->first();
        return $dialogue->id ?? null;
    }

    public function framesDataBuild($dialogueId)
    {
        $dialogues = Intro::where('sub_chapter_id', $dialogueId)->get();
        $frames = [];
        foreach ($dialogues as $item) {
            if ($item->content_type == '3' && $item->text_en <> null) {
                continue;
            } else {
                $frames[] = [
                    'id' => $item->id,
                    'content_type' => $item->content_type,
                ];
            }
        }
        return $frames;
    }

    private function backgroundGet($item, $size, $v2, $background = null)
    {
        if (array_key_exists($item->id, $this->dialogues)) {
            $DialogueFrame = $this->dialogues[$item->id];
        } else {
            $DialogueFrame = $this->dialogues[$item->id] = Intro::find($item->id);
        }
        $backgroundFunctions = new Backgrounds();
        if ($v2) {
            $background = $backgroundFunctions->backgroundV2Get($DialogueFrame, $size, $background);
        } else {
            $background = $backgroundFunctions->backgroundGet($DialogueFrame, $size);
        }
        return $background;
    }
}
