<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\Api;
use App\Http\Controllers\Services\Dialogues;
use App\Http\Controllers\Services\Games;
use App\Http\Controllers\Services\HostChecker;
use App\Http\Controllers\Services\Images;
use App\Http\Controllers\Services\Info;
use App\Http\Controllers\Services\IntroductionWelcome;
use App\Http\Controllers\Services\Parser\PracticeText;
use App\Http\Controllers\Services\Practice;
use App\Http\Controllers\Services\Summary;
use App\Http\Controllers\Services\Text;
use App\Http\Controllers\Services\Theory;
use App\Models\Background;
use App\Models\Chapter;
use App\Models\Emotion;
use App\Models\Episode;
use App\Models\File;
use App\Models\Game;
use App\Models\GameDescription;
use App\Models\GameDescriptionPivot;
use App\Models\GameWord;
use App\Models\Intro;
use App\Models\Introduction;
use App\Models\Language;
use App\Models\Learning;
use App\Models\Motivation;
use App\Models\Personage;
use App\Models\SubChapter;
use App\Models\Subtitle;
use App\Models\Welcome;
use App\Models\Word;
use Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class V2SceneApiController extends Controller
{
    private $versions = [
        'v2' => true,
    ];
    private $mediaFilesType = [
        '0' => 3,
        '1' => 2,
        '2' => 1
    ];
    private $type;
    private $introduction = false;
    private $motivation = false;
    private $welcome = false;
    private $chapter;
    private $episode;
    private $background;
    private $learningWithTheories;
    private $games = [];
    private $gameDescription = [];
    private $gameTemplates = [];
    private $practiceGameTemplateLearning = [];
    private $practiceArr = [];

    private function getHost()
    {
        $hostCheck = new HostChecker();
        return $hostCheck->hostCheck(config('values.host'));
    }

    private function otherElementsCheck($other)
    {
        if (isset($other)) {
            foreach ($other as $item) {
                switch ($item) {
                    case (1):
                        $this->welcome = true;
                        break;
                    case (2):
                        $this->introduction = true;
                        break;
                    case (3):
                        $this->motivation = true;
                        break;
                    default:
                        break;
                }
            }
        }
    }

    private function taskDataBuild($fullTask, $textTaskData, $imageTaskData, $audioTaskData, $item, $practiceFunctions, $textFunctions, $randomLocalise, $lcid, $size, $apiFunctions)
    {
        $task = null;
        $mission = null;
        if (array_key_exists($item->id, $this->games)) {
            $gameTemplateData = $this->games[$item->id]->gamesTemplate[0];
        } else {
            $this->games[$item->id] = Game::with('gamesTemplate', 'theories', 'practices')->find($item->id);
            $gameTemplateData = $this->games[$item->id]->gamesTemplate[0];
        }
        if (in_array($practiceFunctions->typeGet($gameTemplateData
            ->id, $this->gameTemplates)[0], [1, 6, 5])) {
            if ($gameTemplateData
                    ->task == "audio") {
//                                        $imagesFunctions = new Images();
//                                        $timeStamps = $item->updated_at->getTimestamp();
//                                        $mediaUrl = $textFunctions->onlyLocalisationGameGet('en', 'mission', $item);
                $mission = $textFunctions->onlyLocalisationGameGet('en', 'mission', $item);
            }
            if ($gameTemplateData
                    ->task == "text")
                if ($randomLocalise == 0)
                    $mission = $textFunctions->onlyLocalisationGameGet($lcid, 'mission', $item);
                else
                    $mission = $textFunctions->onlyLocalisationGameGet('en', 'mission', $item);
            if ($gameTemplateData
                    ->task == "image") {
//                                        $imagesFunctions = new Images();
//                                        $timeStamps = $item->updated_at->getTimestamp();
//                                        $mediaUrl = $textFunctions->onlyLocalisationGameGet($lcid, 'mission', $item);
                $mission = $textFunctions->onlyLocalisationGameGet($lcid, 'mission', $item);
            }
            if ($gameTemplateData
                    ->task == "space") {
                if ($gameTemplateData
                        ->additional_task == 'audio') {
                    $mission = $textFunctions->onlyLocalisationGameGet('en', 'mission', $item);
                    $mission = json_decode($mission);
                    $answer = json_decode($item->answer);
                    foreach ($mission as $key => $miss) {
                        $i = 0;
                        $splitedArray = str_split($miss);
                        foreach ($splitedArray as $index => $letter) {
                            if ($letter == '@') {
                                $splitedArray[$index] = $answer[$key][$i];
                            }
                        }
                        $mission[$key] = implode('', $splitedArray);
                    }
                }
            }
            if (!is_array($mission))
                if (json_decode($mission) != null) {
                    $mission = json_decode($textFunctions->onlyLocalisationGameGet($lcid, 'mission', $item));
                }
        } elseif (in_array($practiceFunctions->typeGet($gameTemplateData
            ->id, $this->gameTemplates)[0], [3, 0])) {
            if ($gameTemplateData
                    ->task == "audio") {
//                                        $imagesFunctions = new Images();
//                                        $timeStamps = $item->updated_at->getTimestamp();
//                                        $mediaUrl = $textFunctions->onlyLocalisationGameGet('en', 'mission', $item);
                $mission = $textFunctions->onlyLocalisationGameGet('en', 'mission', $item);
            }
            if ($gameTemplateData
                    ->task == "text")
                $mission = $textFunctions->onlyLocalisationGameGet($lcid, 'mission', $item);
            if ($gameTemplateData
                    ->task == "image") {
//                                        $imagesFunctions = new Images();
//                                        $timeStamps = $item->updated_at->getTimestamp();
//                                        $mediaUrl = $textFunctions->onlyLocalisationGameGet($lcid, 'mission', $item);
                $mission = $textFunctions->onlyLocalisationGameGet($lcid, 'mission', $item);
            }
            if ($mission != null) {
                if (json_decode($mission) <> null) {
                    $mission = json_decode($textFunctions->onlyLocalisationGameGet($lcid, 'mission', $item));
                }
            }
        } elseif ($practiceFunctions->typeGet($gameTemplateData
                ->id, $this->gameTemplates)[0] == 4) {
            if ($gameTemplateData
                    ->task == "audio") {
//                                        $imagesFunctions = new Images();
//                                        $timeStamps = $item->updated_at->getTimestamp();
//                                        $mediaUrl = $textFunctions->onlyLocalisationGameGet('en', 'mission', $item);
                $mission = $textFunctions->onlyLocalisationGameGet('en', 'mission', $item);
            } else
                $mission = $textFunctions->onlyLocalisationGameGet($lcid, 'mission', $item);
        } elseif (in_array($practiceFunctions->typeGet($gameTemplateData
            ->id, $this->gameTemplates), [7, 8])) {
            $mission = json_decode($textFunctions->onlyLocalisationGameGet('en', 'mission', $item));
        } else {
            $mission = json_decode($textFunctions->onlyLocalisationGameGet($lcid, 'mission', $item));
        }
        $type = $practiceFunctions->typeOfTaskGet($gameTemplateData);
        [$gameType, $this->gameTemplates] = $practiceFunctions->typeGet($gameTemplateData->id, $this->gameTemplates);

        if ($type == 1) {
            if (isset($mission)) {
                foreach ($mission as $key => $value) {
                    $mission[$key] .= "_$size.webp";
                }

            } else {
                $mission[] = null;
            }
        }
        $addTaskType = null;
        switch ($gameType) {
            case (0):
                //erudite
                if (isset($gameTemplateData->additional_task)) {
                    $addTaskType = $practiceFunctions->typeOfAdditionalTaskGet($gameTemplateData);
                }
                if (isset($addTaskType)) {
                    $fullTask [] = [
                        'id' => $item->id,
                        'data' =>
                            [
                                ['type' => $type,
                                    'id' => $item->id],

                                ['type' => $addTaskType,
                                    'id' => $item->id],

                            ]
                    ];
                } else {
                    $fullTask [] = [
                        'id' => $item->id,
                        'data' =>
                            [
                                ['type' => $type,
                                    'id' => $item->id],

                            ]
                    ];
                }
                if (is_array($mission)) {
                    $task[] = $mission;

                } else {
                    $task[] = [$mission];
                }
                if (isset($addTaskType)) {
                    if ($addTaskType == 1) {
                        if (isset($item->theory))
                            $addTask = json_decode($item->theory);
                    } else
                        if (isset($item->link_words))
                            $addTask = json_decode($item->link_words);
                }
                break;
            case (6):
            case (5):
            case (1):
                //1 millionaire
                if ($gameTemplateData->task == 'space') {
                    if ($gameTemplateData->additional_task == 'audio') {
                        $fullTask [] = [
                            'id' => $item->id,
                            'data' =>
                                [
                                    ['type' => $type,
                                        'id' => $item->id]
                                ]
                        ];
                        if (is_array($mission)) {
                            foreach ($mission as $key => $miss)
                                if ($key == 0)
                                    $task .= $miss;
                                else
                                    $task .= ', ' . $miss;
                        } else {
                            $task = $mission;
                        }
                    } elseif ($gameTemplateData->additional_task == 'image') {
                        $addTaskType = $practiceFunctions->typeOfAdditionalTaskGet($gameTemplateData);
                        $fullTask [] = [
                            'id' => $item->id,
                            'data' =>
                                [
                                    ['type' => 1,
                                        'id' => $item->id]
                                ]
                        ];
                        $task = $mission;
                    }
                } else {
                    if (isset($gameTemplateData->additional_task)) {
                        $addTaskType = $practiceFunctions->typeOfAdditionalTaskGet($gameTemplateData);
                    }

                    if (isset($addTaskType)) {
                        $fullTask [] = [
                            'id' => $item->id,
                            'data' =>
                                [
                                    ['type' => $type,
                                        'id' => $item->id],

                                    ['type' => $addTaskType,
                                        'id' => $item->id],

                                ]
                        ];
                    } else {
                        $fullTask [] = [
                            'id' => $item->id,
                            'data' =>
                                [
                                    ['type' => $type,
                                        'id' => $item->id]
                                ]
                        ];
                    }
                    if (is_array($mission)) {
                        $task = $mission;
                    } else {
                        $task = [$mission];
                    }
                }

                if (isset($addTaskType)) {
                    if ($addTaskType == 1) {
                        if (isset($item->theory)) {
                            $addTask = json_decode($item->theory);
                            foreach ($addTask as $index => $img) {
                                $addTask[$index] = $img . "_$size.webp";
                            }
                        }

                    } else
                        if (isset($item->link_words))
                            $addTask = json_decode($item->link_words);
                }

                break;
            case (2):
                //linkWords
                if (isset($gameTemplateData->additional_task)) {
                    $addTaskType = $practiceFunctions->typeOfAdditionalTaskGet($gameTemplateData);
                    $fullTask [] = [
                        'id' => $item->id,
                        'data' =>
                            [
                                ['type' => $addTaskType,
                                    'id' => $item->id]
                            ]
                    ];
                    if ($addTaskType == 1) {
                        if (isset($item->theory)) {
                            $addTask = json_decode($item->theory);
                            foreach ($addTask as $index => $img) {
                                $addTask[$index] = $img . "_$size.webp";
                            }
                        }
                    } else
                        if (isset($item->link_words))
                            $addTask = json_decode($item->link_words);
                }
                break;
            case (3):
                //typeAnswer

                if (isset($gameTemplateData->additional_task)) {
                    $addTaskType = $practiceFunctions->typeOfAdditionalTaskGet($gameTemplateData);
                    $fullTask [] = [
                        'id' => $item->id,
                        'data' =>
                            [
                                [
                                    'type' => $type,
                                    'id' => $item->id
                                ],
                                [
                                    'type' => $addTaskType,
                                    'id' => $item->id
                                ]
                            ]
                    ];
                    if ($addTaskType == 1) {
                        if (isset($item->theory)) {
                            $addTask = json_decode($item->theory);
                            foreach ($addTask as $index => $img) {
                                $addTask[$index] = $img . "_$size.webp";
                            }
                        }
                    }
                } else {
                    $fullTask [] = [
                        'id' => $item->id,
                        'data' =>
                            [
                                ['type' => $type,
                                    'id' => $item->id]
                            ]
                    ];
                }
                if (is_array($mission)) {
                    $task[] = $mission;
                } else {
                    $task[] = [$mission];
                }
                break;
            case (4):
            case (10):

                //makeASentence

                if (isset($gameTemplateData->additional_task)) {
                    $addTaskType = $practiceFunctions->typeOfAdditionalTaskGet($gameTemplateData);
                    $fullTask [] = [
                        'id' => $item->id,
                        'data' =>
                            [
                                [
                                    'type' => $type,
                                    'id' => $item->id
                                ],
                                [
                                    'type' => $addTaskType,
                                    'id' => $item->id
                                ]
                            ]
                    ];
                    if ($addTaskType == 1) {
                        if (isset($item->theory)) {
                            $addTask = json_decode($item->theory);
                            foreach ($addTask as $index => $img) {
                                $addTask[$index] = $img . "_$size.webp";
                            }
                        }
                    } else {
                        if (isset($item->link_words))
                            $addTask = json_decode($item->link_words);
                    }
                } else {
                    $fullTask [] = [
                        'id' => $item->id,
                        'data' =>
                            [
                                ['type' => $type,
                                    'id' => $item->id]
                            ]
                    ];
                }
                if (is_array($mission)) {
                    $task[] = $mission;
                } else {
                    $task[] = [$mission];
                }
                break;

            case (9):
                //trueOrFalse

                if (isset($gameTemplateData->additional_task)) {
                    $addTaskType = $practiceFunctions->typeOfAdditionalTaskGet($gameTemplateData);
                    $fullTask [] = [
                        'id' => $item->id,
                        'data' =>
                            [
                                [
                                    'type' => $type,
                                    'id' => $item->id
                                ],
                                [
                                    'type' => $addTaskType,
                                    'id' => $item->id
                                ]
                            ]
                    ];
                    if ($addTaskType == 1) {
                        if (isset($item->theory)) {
                            $addTask = json_decode($item->theory);
                            foreach ($addTask as $index => $img) {
                                $addTask[$index] = $img . "_$size.webp";
                            }
                        }
                    } else {
                        if (isset($item->link_words))
                            $addTask = json_decode($item->link_words);
                    }
                } else {
                    $fullTask [] = [
                        'id' => $item->id,
                        'data' =>
                            [
                                ['type' => $type,
                                    'id' => $item->id]
                            ]
                    ];
                }
//                                    if (is_array($mission)) {
//                                        $task[] = $mission[0] ?? null;
//                                    } else {
//                                        $task[] = $mission;
//                                    }

                break;
            case (8);
            case (7):
                //insertSymbols
                if (is_array($mission)) {
                    $task[] = $mission[0];
                } else {
                    $task[] = $mission;
                }
                if (isset($task[0]))
                    $fullTask [] = [
                        'id' => $item->id,
                        'data' =>
                            [
                                ['type' => $type,
                                    'id' => $item->id]
                            ]
                    ];

                break;
            default:
                $addTask = null;
                break;
        }
        $task = $apiFunctions->nullCheck($task);
        if (isset($task)) {
            switch ($type) {
                case (2):
                    $audioUrl = null;
                    $timeStamps = null;
                    if ($item->audio == 1) {
                        $audioUrl = config('values.api_url') . 'game/audio/' . $this->getHost() . '/' . str_replace(' ', '_', $item->id) . '.mp3';
                    }
                    if ($item->input_type == 1) {
                        $wordsFromGame = GameWord::where('game_id', $item->id)->where('type', 0)->first();
                        $wordFromTask = Word::find($wordsFromGame->word_id);
                        $timeStamps = $wordFromTask->updated_at->getTimestamp();
                        $readType = '_word.mp3';
                        if ($item->read_type == 1) {
                            $readType = '_letter.mp3';

                        }
                        if ($readType == '_word.mp3' && $wordFromTask->by_word == 1) {
                            $audioUrl = config('values.api_url') . 'word/audio/' . $this->getHost() . '/' . str_replace(' ', '_', $wordFromTask->word) . $readType;
                        }
                        if ($readType == '_letter.mp3' && $wordFromTask->by_letter == 1) {
                            $audioUrl = config('values.api_url') . 'word/audio/' . $this->getHost() . '/' . str_replace(' ', '_', $wordFromTask->word) . $readType;
                        }

                    }
                    if (is_array($task)) {
                        $imagesFunctions = new Images();
                        if ($timeStamps == null) {
                            $timeStamps = $item->updated_at->getTimestamp();
                        }
                        $newAudioUrlModel = $imagesFunctions->apiMediaModel($audioUrl, $timeStamps, 1);
                        if (is_array($task[0])) {

                            $audioTaskData[] = [
                                'id' => $item->id,
                                'text' => (string)$task[0][0],
                                'vocalization_type' => $item->read_type,
                                'audio' => $newAudioUrlModel
                            ];
                        } else {
                            $audioTaskData[] = [
                                'id' => $item->id,
                                'text' => (string)$task[0],
                                'vocalization_type' => $item->read_type,
                                'audio' => $newAudioUrlModel

                            ];
                        }

                    } else {
                        $imagesFunctions = new Images();
                        $timeStamps = $item->updated_at->getTimestamp();
                        $newAudioUrlModel = $imagesFunctions->apiMediaModel($audioUrl, $timeStamps, 1);
                        $audioTaskData[] = [
                            'id' => $item->id,
                            'text' => (string)$task,
                            'vocalization_type' => $item->read_type,
                            'audio' => $newAudioUrlModel

                        ];
                    }


                    break;
                case (1):
                    $imagesFunctions = new Images();
                    $timeStamps = $item->updated_at->getTimestamp();
                    $hint = null;
                    if ($item->input_type == 1) {
                        $wordsFromGame = GameWord::where('game_id', $item->id)->where('type', 2)->first();
                        if ($item->hint_option == 1) {
                            $hintFromGame = GameWord::where('game_id', $item->id)->where('type', 4)->first();
                            if (isset($hintFromGame)) {
                                $hintWord = Word::find($hintFromGame->word_id);
                                if ($lcid == "en") {
                                    $hint = $hintWord->definition_en ?? null;
                                } else {
                                    $hint = $hintWord['meaning_' . $lcid];
                                }
                            }
                        } else {
                            $hint = $item['hint_' . $lcid] ?? $item['hint_en'];
                        }
                        $wordFromTask = Word::find($wordsFromGame->word_id);

                        $timeStamps = $wordFromTask->updated_at->getTimestamp();
                        $mediaUrl = Storage::disk('gcs')->url("word/". $wordFromTask->id . "_$size.webp");
                        $newMediaModel = $imagesFunctions->apiMediaModel($mediaUrl, $timeStamps, 0);
                        $imageTaskData[] = [
                            'id' => $item->id,
                            'image' => $newMediaModel,
                            'hint' => $hint ?? null,
                        ];
                    } else {
                        if ($item->hint_option == 1) {
                            $hintFromGame = GameWord::where('game_id', $item->id)->where('type', 4)->first();
                            if (isset($hintFromGame)) {
                                $hintWord = Word::find($hintFromGame->word_id);
                                if ($lcid == "en") {
                                    $hint = $hintWord->definition_en ?? null;
                                } else {
                                    $hint = $hintWord['meaning_' . $lcid];
                                }
                            }
                        } else {
                            $hint = $item['hint_' . $lcid] ?? $item['hint_en'];
                        }
                        if (is_array($task[0])) {
                            $mediaUrl = $task[0][0];
                            $newMediaModel = $imagesFunctions->apiMediaModel($mediaUrl, $timeStamps, 0);

                            $imageTaskData[] = [
                                'id' => $item->id,
                                'image' => $newMediaModel,
                                'hint' => $hint,

                            ];
                        } else {
                            if ($item->hint_option == 1) {
                                $hintFromGame = GameWord::where('game_id', $item->id)->where('type', 4)->first();
                                if (isset($hintFromGame)) {
                                    $hintWord = Word::find($hintFromGame->word_id);
                                    if ($lcid == "en") {
                                        $hint = $hintWord->definition_en ?? null;
                                    } else {
                                        $hint = $hintWord['meaning_' . $lcid];
                                    }
                                }
                            } else {
                                $hint = $item['hint_' . $lcid] ?? $item['hint_en'];
                            }
                            $mediaUrl = $task[0];
                            $newMediaModel = $imagesFunctions->apiMediaModel($mediaUrl, $timeStamps, 0);
                            $imageTaskData[] = [
                                'id' => $item->id,
                                'image' => $newMediaModel,
                                'hint' => $hint,

                            ];
                        }
                    }
                    break;
                default:
                    if ($gameTemplateData->task <> 'space') {
                        if (!is_array($task)) {
                            $textTaskData[] = [
                                'id' => $item->id,
                                'data' => [(string)$task],
                            ];
                        } else {
                            if (is_array($task[0])) {
                                if (isset($task[0][0]) && !empty($task[0][0])) {
                                    $task[0][0] = (string)$task[0][0];
                                    $textTaskData[] = [
                                        'id' => $item->id,
                                        'data' => $task[0],
                                    ];
                                }
                            } else {
                                if (isset($task[0]) && !empty($task[0])) {
                                    $task[0] = (string)$task[0];
                                    $textTaskData[] = [
                                        'id' => $item->id,
                                        'data' => $task,
                                    ];
                                }
                            }
                        }
                    }

                    break;
            }
        }

        if (isset($addTask)) {
            $addTask = $apiFunctions->nullCheck($addTask);
            if (isset($addTask)) {
                switch ($addTaskType) {
                    case(2):
                        if (is_array($addTask)) {
                            if (is_array($addTask[0])) {
                                $audioTaskData[] = [
                                    'id' => $item->id,
                                    'text' => (string)$addTask[0][0],
                                    'vocalization_type' => $item->read_type ?? 1,
                                    'audio' => null

                                ];
                            } else {
                                $audioTaskData[] = [
                                    'id' => $item->id,
                                    'text' => (string)$addTask[0],
                                    'vocalization_type' => $item->read_type ?? 1,
                                    'audio' => null

                                ];
                            }

                        } else {
                            $audioTaskData[] = [
                                'id' => $item->id,
                                'text' => (string)$addTask,
                                'vocalization_type' => $item->read_type ?? 1,
                                'audio' => null

                            ];
                        }
                        break;
                    case (1):
                        $imagesFunctions = new Images();
                        $timeStamps = $item->updated_at->getTimestamp();
                        if (is_array($addTask)) {
                            $newMediaModel = $imagesFunctions->apiMediaModel((string)$addTask[0], $timeStamps, 0);
                            $imageTaskData[] = [
                                'id' => $item->id,
                                'image' => $newMediaModel,
                                'hint' => $item['hint_' . $lcid] ?? $item['hint_en'],

                            ];
                        } else {
                            $newMediaModel = $imagesFunctions->apiMediaModel((string)$addTask, $timeStamps, 0);
                            $imageTaskData[] = [
                                'id' => $item->id,
                                'image' => $newMediaModel,
                                'hint' => $item['hint_' . $lcid] ?? $item['hint_en'],

                            ];
                        }
                        break;
                    default:
                        if ($gameTemplateData->task <> 'space') {
                            if (isset($addTaskType)) {
                                $textTaskData[] = [
                                    'id' => $item->id,
                                    'data' => $addTask,
                                ];
                            }
                        }
                        break;
                }
            }
        }
        return [$fullTask, $textTaskData, $imageTaskData, $audioTaskData];
    }

    private function practiceFramesBuildData($practiceGamesData, $learning)
    {
        [$keys, $values] = Arr::divide($practiceGamesData);
        $practiceFunctions = new Practice();
        $practiceFrames = [];
        $type = null;
        $learningId = [];
        try {
            $learningId[] = $learning->id;
        } catch (\Exception $e) {
            foreach ($learning as $item) {
                $learningId[] = $item->id;
            }
        }

        if (isset($keys) && !empty($keys)) {
            foreach ($keys as $gameType) {
                $type [] = $practiceFunctions->getType($gameType);
            }
        }
        if (isset($values) && !empty($values)) {
            foreach ($values as $key => $item) {
                if (isset($item) && !empty($item)) {
                    foreach ($item as $game) {
                        if (array_key_exists($game['id'], $this->games)) {
                            $gameModel = $this->games[$game['id']];
                        } else {
                            $gameModel = $this->games[$game['id']] = Game::with(['gamesTemplate', 'theories', 'practices'])->find($game['id']);
                        }
                        $gameTemplate = $gameModel;
                        if (!isset($gameModel)) {
                            if (array_key_exists($game['id'], $this->games)) {
                                $gameTemplate = $this->games[$game['id']]->gamesTemplate[0];
                            } else {
                                $this->games[$game['id']] = Game::with(['gamesTemplate', 'theories', 'practices'])->find($game['id']);
                                $gameTemplate = $this->games[$game['id']]->gamesTemplate[0];
                            }
//                            $gameTemplate = Game::with(['gamesTemplate', 'theories', 'practices'])->find($game['id'])->gamesTemplate[0];
                        }
                        $description_id = $gameTemplate->game_description_id ?? null;
                        if (!isset($description_id)) {
                            if (array_key_exists($gameTemplate->practices[0]->id . $gameTemplate->gamesTemplate[0]->id, $this->practiceGameTemplateLearning)) {
                                $description = $this->practiceGameTemplateLearning[$gameTemplate->practices[0]->id . $gameTemplate->gamesTemplate[0]->id];
                            } else {
                                $description = $this->practiceGameTemplateLearning[$gameTemplate->practices[0]->id . $gameTemplate->gamesTemplate[0]->id] = GameDescriptionPivot::where('practice_id', $gameTemplate->practices[0]->id)
                                    ->whereIn('learning_id', $learningId)
                                    ->where('game_template_id', $gameTemplate->gamesTemplate[0]->id)->first();
                            }


                            if (isset($description)) {
                                $description_id = $description->game_description_id;
                            }
                        }
                        $practiceFrames[] = [
                            'frame_id' => $game['id'],
                            'type' => $type[$key],
                            "descriptionId" => $description_id,
                        ];
                    }
                }
            }
        }


        return $practiceFrames;

    }

    public function sceneGet(Request $request, $version)
    {

        if ($this->versions[$version]) {

            // if 0 -> answer will be in english
            $randomLocalise = rand(0, 1);

            if (array_key_exists('sub-chapter_id', $request->all())) {
                $id = (int)$request['sub-chapter_id'];
                if ($id == 0) {
                    return response('bad sub-chapter_id', 400);
                }
            } else {
                return response('bad sub-chapter_id', 400);
            }
            $scene = SubChapter::find($id);
            if (isset($scene)) {
                $this->chapter = Chapter::find($scene->chapter_id);
            } else {
                return response('not found', 404);
            }
            $this->type = $this->chapter->type;

//        $size = "L";
            $apiFunctions = new Api();

            if (array_key_exists('icons_scale', $request->all())) {
                $image = new Images();
                $size = $image->iconScaleCheck($request->icons_scale);
            } else return response('bad icons_scale', 400);

            if (array_key_exists('web', $request->all())) {
                $short = $request->web;
                if ($short <> 0 && $short <> 1) {
                    $short = 0;
                }
            } else {
                $short = 0;
            }
            if (array_key_exists('lcid', $request->all())) {
                $lcid = $request->lcid;
                if ($apiFunctions->lcidExistingCheck($lcid, $short) <> $lcid) {
                    return response('bad lcid', 400);
                }
            } else {
                $lcid = "none";
            }
            $this->episode = Episode::find($this->chapter->episode_id);
            if (isset($this->episode)) {
                $this->background = Background::find($this->episode->background_id);
            }

            $dialogue = SubChapter::where('sub_chapter_id', $id)->where('short', $short)->first();
//        if ($this->type <> 1) {
//            if (!isset($dialogue)) {
//                return response('There are no dialogue linked', 400);
//            }
//        }

            $theoryFunctions = new Theory();
            $summaryFunctions = new Summary();

            $info = new Info();
            $infoData = $info->infoDataBuild($scene, $dialogue, $lcid, $size, true, $this->chapter, $this->episode, $this->background);


            $introductionWelcomeFunctions = new IntroductionWelcome();
            $summary = $summaryFunctions->getSummaryRecursive(0, $lcid, $scene);

            if (isset($summary) && !empty($summary) && isset($summary[0])) {

                $chapter = $this->chapter ?? Chapter::find($scene->chapter_id);
                $episodeBackground = null;

                if (isset($chapter)) {

                    if (isset($this->episode)) {
                        if (isset($this->episode->background_id)) {
                            $background = $this->background ?? Background::find($this->episode->background_id);
                            $imagesFunctions = new Images();
                            $timeStamps = $background->updated_at->getTimestamp();
                            $mediaUrl = Storage::disk('gcs')->url("backgrounds/$background->name" . "_$size.webp");
                            $episodeBackground = $imagesFunctions->apiMediaModel($mediaUrl, $timeStamps, 0);
                        }

                    }
                }
                $summaryData = [
                    'id' => 0,
                    'nextId' => null,
                    'text' => $summary[1] ?? null,
                    'english_text' => $summary[0] ?? null,
                    'background' => $episodeBackground,
                    'characters' => [],
                ];
            } else {
                $summaryData = null;
            }

            $introduction = new Introduction();
            $welcome = new Welcome();
            $motivations = new Motivation();

            if ($short == 1) {
                [$introductionData, $this->learningWithTheories] = $introductionWelcomeFunctions->DataBuild($scene, $lcid, $size, $introduction, $this->type, 3, true, $this->chapter, $this->episode, $this->background);
            } else {
                [$introductionData, $this->learningWithTheories] = $introductionWelcomeFunctions->DataBuild($scene, $lcid, $size, $introduction, $this->type, 1, true, $this->chapter, $this->episode, $this->background, $this->learningWithTheories);
            }
            if ($this->episode->type == 1) {
                $welcomeData = $introductionWelcomeFunctions->DataBuild($scene, $lcid, $size, $welcome, $this->type, 4, true, $this->chapter, $this->episode, $this->background);
            } else {
                $welcomeData = $introductionWelcomeFunctions->DataBuild($scene, $lcid, $size, $welcome, $this->type, 0, true, $this->chapter, $this->episode, $this->background);
            }
            $motivationData = $introductionWelcomeFunctions->DataBuild($scene, $lcid, $size, $motivations, $this->type, 2, true, $this->chapter, $this->episode, $this->background);

            $practiceFunctions = new PracticeApiController();
            $dialogueDataBuild = new Dialogues();
            $firstOneId = null;
            $frames = null;
            $default_frames_data = null;
            $choices_frames_data = null;
            $practiceGamesData = [];
            if (isset($dialogue)) {
                $firstOneId = $dialogueDataBuild->getTheFirstOneId($dialogue->id);
                $frames = $dialogueDataBuild->framesDataBuild($dialogue->id);
                if ($short == 0) {
                    $default_frames_data = $dialogueDataBuild->dialogueDefaultFramesDataBuild($dialogue->id, $size, $lcid, true);

                } else {
                    $default_frames_data = $dialogueDataBuild->dialogueDefaultFramesDataBuild($dialogue->id, $size, $lcid, true, null, $this->chapter);
                }
                $choices_frames_data = $dialogueDataBuild->dialogueChoiceFramesDataBuild($dialogue->id, $size, $lcid, true);
            }
            $dialogueData = [
                'first_frame_id' => $firstOneId,
                'frames' => $frames,
                'default_frames_data' => $default_frames_data,
                'choices_frames_data' => $choices_frames_data,
            ];

            if (!isset($firstOneId)) {
                $dialogueData = null;
            }

            $textFunctions = new Text();
            $data = [];
            $gameFunctions = new Games();
            if (isset($scene->learning_id) || isset($scene->learnings)) {
                if ($this->type <> 1 && $this->type <> 3) {
                    if (!isset($this->learningWithTheories)) {
                        $learning = $this->learningWithTheories = Learning::with('theories', 'practices', 'games')->find($scene->learning_id);
                    } else {
                        $learning = $this->learningWithTheories;
                    }
                } else {
                    $learning = Learning::with('practices', 'games')->findMany($scene->learnings);
                }
                $descriptions = [];
                $uniqueGameDescription = null;
                if (isset($learning)) {
                    if ($this->type == 1 || $this->type == 3) {
                        foreach ($learning as $oneLearning) {
                            [$practiceGamesData[], $this->games, $this->gameTemplates, $this->practiceArr] = $practiceFunctions->practiceGet($oneLearning, $size, $lcid, $randomLocalise, $this->games, $this->gameTemplates, $this->practiceArr);
                        }
                    } else {
                        [$practiceGamesData, $this->games, $this->gameTemplates, $this->practiceArr] = $practiceFunctions->practiceGet($learning, $size, $lcid, $randomLocalise, $this->games, $this->gameTemplates, $this->practiceArr);
                    }
                    if ($this->type <> 1 && $this->type <> 3) {
                        foreach ($learning->games as $item) {
                            if (isset($item->game_description_id)) {
                                if (array_key_exists($item->game_description_id, $this->gameDescription)) {
                                    $uniqueGameDescription[] = $this->gameDescription[$item->game_description_id];
                                } else {
                                    $uniqueGameDescription [] = $this->gameDescription[$item->game_description_id] = GameDescription::find($item->game_description_id);
                                }
                            }
                        }
                    } else {
                        foreach ($learning as $values) {
                            foreach ($values->games as $item) {
                                if (isset($item->game_description_id)) {
                                    if (array_key_exists($item->game_description_id, $this->gameDescription)) {
                                        $uniqueGameDescription[] = $this->gameDescription[$item->game_description_id];
                                    } else {
                                        $uniqueGameDescription [] = $this->gameDescription[$item->game_description_id] = GameDescription::find($item->game_description_id);
                                    }

                                }
                            }
                        }
                    }
                    if (isset($uniqueGameDescription))
                        foreach ($uniqueGameDescription as $item)
                            if ($gameFunctions->descriptionDouplicatEliminate($descriptions, $item->id) <> null) {
                                $descriptions[] = [
                                    'id' => $item->id ?? null,
                                    'data' => $textFunctions->onlyLocalisationGameGet($lcid, 'description', $item),
                                ];
                            }
                    if ($this->type <> 1 && $this->type <> 3) {
                        foreach ($learning->practices as $item) {
                            if (array_key_exists($item->id, $this->practiceArr)) {
                                $gameTemplate = $this->practiceArr[$item->id]->gameTemplates;
                            } else {
                                $this->practiceArr[$item->id] = \App\Models\Practice::with('gameTemplates', 'games')->find($item->id);
                                $gameTemplate = $this->practiceArr[$item->id]->gameTemplates;
                            }

                            foreach ($gameTemplate as $value) {
                                $gameDescription = GameDescriptionPivot::where('game_template_id', $value->id)
                                    ->where('practice_id', $item->id)
                                    ->where('learning_id', $learning->id)->first();

                                if (isset($gameDescription)) {
                                    if (array_key_exists($gameDescription->game_description_id, $this->gameDescription)) {
                                        $gameDesccriptionText = $this->gameDescription[$gameDescription->game_description_id];
                                    } else {
                                        $gameDesccriptionText = $this->gameDescription[$gameDescription->game_description_id] = GameDescription::find($gameDescription->game_description_id);
                                    }
                                    $i = 0;
                                    foreach ($descriptions as $descr) {
                                        if ($descr['id'] == $gameDescription->game_description_id) {
                                            $i = 1;
                                            break;
                                        }
                                    }
                                    if ($i == 0) {
                                        if ($gameFunctions->descriptionDouplicatEliminate($descriptions, $gameDescription->game_description_id) <> null) {
                                            $descriptions[] = [
                                                'id' => $gameDescription->game_description_id ?? null,
                                                'data' => $textFunctions->onlyLocalisationGameGet($lcid, 'description', $gameDesccriptionText)
                                            ];
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        foreach ($learning as $values) {
                            foreach ($values->practices as $item) {
                                if (array_key_exists($item->id, $this->practiceArr)) {
                                    $gameTemplate = $this->practiceArr[$item->id]->gameTemplates;
                                } else {
                                    $this->practiceArr[$item->id] = \App\Models\Practice::with('gameTemplates', 'games')->find($item->id);
                                    $gameTemplate = $this->practiceArr[$item->id]->gameTemplates;
                                }
                                foreach ($gameTemplate as $value) {
                                    $gameDescription = GameDescriptionPivot::where('game_template_id', $value->id)
                                        ->where('practice_id', $item->id)
                                        ->where('learning_id', $values->id)->first();

                                    if (isset($gameDescription)) {
                                        $gameDesccriptionText = GameDescription::find($gameDescription->game_description_id);
                                        $i = 0;
                                        foreach ($descriptions as $descr) {
                                            if ($descr['id'] == $gameDescription->game_description_id) {
                                                $i = 1;
                                                break;
                                            }
                                        }
                                        if ($i == 0) {
                                            $descriptions[] = [
                                                'id' => $gameDescription->game_description_id ?? null,
                                                'data' => $textFunctions->onlyLocalisationGameGet($lcid, 'description', $gameDesccriptionText)
                                            ];
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $quizPracticesWithoutGames = [];
                    $practicesWithoutGames = [];
                    if ($this->type <> 1 && $this->type <> 3) {
                        $practicesWithoutGames = $learning->practices;
                    } else {
                        foreach ($learning as $item) {
                            $quizPracticesWithoutGames[] = $item->practices;
                        }
                    }

                    $practicesWithGames = [];
                    $learningPracticesGamesArray = [];

                    if ($this->type == 1 || $this->type == 3) {
                        foreach ($quizPracticesWithoutGames as $practicesWithoutGames) {
                            foreach ($practicesWithoutGames as $item) {
                                if (array_key_exists($item->id, $this->practiceArr)) {
                                    $practicesWithGames[] = $this->practiceArr[$item->id];
                                } else {
                                    $practicesWithGames[] = $this->practiceArr[$item->id] = \App\Models\Practice::with('gameTemplates', 'games')->find($item->id);
                                }
                            }
                        }
                    } else {
                        foreach ($practicesWithoutGames as $item) {
                            if (array_key_exists($item->id, $this->practiceArr)) {
                                $practicesWithGames[] = $this->practiceArr[$item->id];
                            } else {
                                $practicesWithGames[] = $this->practiceArr[$item->id] = \App\Models\Practice::with('gameTemplates', 'games')->find($item->id);
                            }

                        }
                    }
                    $learningGames = false;
                    if ($this->type <> 1 && $this->type <> 3) {
                        if (isset($learning->games)) {
                            $learningGames = true;
                        }
                    } else {
                        foreach ($learning as $values) {
                            if (isset($values->games)) {
                                $learningGames = true;
                            }
                        }
                    }

                    if ($learningGames) {
                        $practiceFunctions = new Practice();
                        foreach ($practicesWithGames as $gamesArr) {
                            if (isset($gamesArr->games)) {
                                $gamesArray = [];
                                foreach ($gamesArr->games as $item) {
                                    if ($this->type <> 1 && $this->type <> 3) {
                                        foreach ($learning->games as $game) {
                                            if ($item->id == $game->id) {
                                                $gamesArray[] = $game;
                                            }
                                        }
                                    } else {
                                        foreach ($learning as $values) {
                                            foreach ($values->games as $game) {
                                                if ($item->id == $game->id) {
                                                    $gamesArray[] = $game;
                                                }
                                            }
                                        }
                                    }
                                }
                                $learningPracticesGamesArray[] = $gamesArray;
                            }
                        }
                        $tasksArray = [];
                        foreach ($learningPracticesGamesArray as $arrayOfPractices) {
//                        $tasks = [];
                            $fullTask = [];
                            $textTaskData = [];
                            $imageTaskData = [];
                            $audioTaskData = [];
                            $practiceArray = [];
                            foreach ($arrayOfPractices as $item) {
                                [$fullTask, $textTaskData, $imageTaskData, $audioTaskData] = $this->taskDataBuild($fullTask, $textTaskData, $imageTaskData, $audioTaskData, $item, $practiceFunctions, $textFunctions, $randomLocalise, $lcid, $size, $apiFunctions);
                            }

                            $tasks = [
                                'tasks_bundle' => $fullTask ?? null,
                                'text_task_data' => $textTaskData,
                                'image_task_data' => $imageTaskData,
                                'audio_task_data' => $audioTaskData,
                            ];
                            $tasksArray[] = $tasks;
                        }
                        if ($this->type == 1 || $this->type == 3) {
                            foreach ($practiceGamesData as $onePracticeGameData) {
                                foreach ($onePracticeGameData as $practiceGamesIncriment => $gamesArr) {
                                    $practice_info = [];
                                    [$keys, $values] = Arr::divide($gamesArr);
                                    $practice = null;

                                    if (isset($values) && !empty($values)) {
                                        foreach ($values as $item) {
                                            if (isset($item)) {
                                                if (array_key_exists(0, $item)) {
                                                    $practice = Game::with('practices')->find($item[0]['id'])->practices->first();
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                    $fileId = null;
                                    if (isset($practice))
                                        $fileId = $practice->file_id;
                                    $file = File::find($fileId);

                                    if ($fileId <> null && isset($file)) {
                                        $mediaFileType = $this->mediaFilesType[$file->type];
                                        switch ($mediaFileType) {
                                            case (2):
                                                $imagesFunctions = new Images();
                                                $url = Storage::disk('gcs')->url("audio/$file->name");
                                                $timeStamps = $file->updated_at->getTimestamp();
                                                $practice_info = [
                                                    'practice_type' => $mediaFileType,
                                                    'audio_data' => $imagesFunctions->apiMediaModel($url, $timeStamps, 1),
                                                ];
                                                if ($short == 1) {
                                                    $practiceTextParcer = new PracticeText();
                                                    $practiceTextDataArray = null;
                                                    if ($practice->subtitle_id <> null) {
                                                        $subtitleCollection = Subtitle::find($practice->subtitle_id);
                                                        if (isset($subtitleCollection)) {
                                                            $practiceTextDataArray = $practiceTextParcer->PracticeTextPageGet(json_decode($subtitleCollection->name_en), $file);
                                                        }
                                                    }
//                                                $practice_info['subtitle'] = $subtitle;
                                                    $practice_info['subtitle'] = $practiceTextDataArray;
                                                }
                                                break;
                                            case (1):
                                                $url = Storage::disk('gcs')->url("video/$file->name");
                                                $imagesFunctions = new Images();
                                                $timeStamps = $file->updated_at->getTimestamp();
                                                $practice_info = [
                                                    'practice_type' => $mediaFileType,
                                                    'video_data' => $imagesFunctions->apiMediaModel($url, $timeStamps, 2),
                                                ];
                                                break;
                                            case (3):
                                                $img = json_decode($file->url);
                                                $mediaData = null;
                                                if (isset($img)) {
                                                    foreach ($img as $image) {
                                                        $mediaData [] = $image . "_$size.webp";
                                                    }
                                                }
                                                $practiceTextParcer = new PracticeText();
                                                $practiceTextDataArray = $practiceTextParcer->PracticeTextPageGet(json_decode($file->descriptions), $file);
                                                $practice_info = [
                                                    'practice_type' => $mediaFileType,
                                                    'text_data' => $practiceTextDataArray
                                                ];
                                                break;
                                            default:
                                                break;
                                        }
                                    } else {
                                        $practice_info = [
                                            'practice_type' => 0,
//                                        'practice_additional_data' => null,
                                        ];
                                    }
                                    $practiceFrames = [
                                        'practiceFrames' => $this->practiceFramesBuildData($gamesArr, $learning),
                                        'descriptions' => $descriptions,
                                        'tasks' => $tasksArray[$practiceGamesIncriment],
                                        'practice_info' => $practice_info
                                    ];
                                    foreach ($values as $key => $item) {
                                        $practiceFrames[$keys[$key]] = $item;
                                    }
                                    $practiceArray [] = $practiceFrames;

                                }
                            }
                        } else {
                            foreach ($practiceGamesData as $practiceGamesIncriment => $gamesArr) {
                                $practice_info = [];
                                [$keys, $values] = Arr::divide($gamesArr);
                                $practice = null;

                                if (isset($values) && !empty($values)) {
                                    foreach ($values as $item) {
                                        if (isset($item)) {
                                            if (array_key_exists(0, $item)) {
                                                if (array_key_exists($item[0]['id'], $this->games)) {
                                                    $practice = $this->games[$item[0]['id']]->practices->first();
                                                } else {
                                                    $this->games[$item[0]['id']] = Game::with('practices')->find($item[0]['id']);
                                                    $practice = $this->games[$item[0]['id']]->practices->first();
                                                }
                                                break;
                                            }
                                        }
                                    }
                                }
                                $fileId = null;
                                if (isset($practice))
                                    $fileId = $practice->file_id;
                                $file = File::find($fileId);

                                if ($fileId <> null && isset($file)) {
                                    $mediaFileType = $this->mediaFilesType[$file->type];
                                    switch ($mediaFileType) {
                                        case (2):
                                            $imagesFunctions = new Images();
                                            $url = Storage::disk('gcs')->url("audio/$file->name");
                                            $timeStamps = $file->updated_at->getTimestamp();
                                            $practice_info = [
                                                'practice_type' => $mediaFileType,
                                                'audio_data' => $imagesFunctions->apiMediaModel($url, $timeStamps, 1),
                                            ];
                                            if ($short == 1) {
                                                $practiceTextParcer = new PracticeText();
                                                $practiceTextDataArray = null;
                                                if ($practice->subtitle_id <> null) {
                                                    $subtitleCollection = Subtitle::find($practice->subtitle_id);
                                                    if (isset($subtitleCollection)) {
                                                        $practiceTextDataArray = $practiceTextParcer->PracticeTextPageGet(json_decode($subtitleCollection->name_en), $file);
                                                    }
                                                }
//                                                $practice_info['subtitle'] = $subtitle;
                                                $practice_info['subtitle'] = $practiceTextDataArray;
                                            }
                                            break;
                                        case (1):
                                            $url = Storage::disk('gcs')->url("video/$file->name");
                                            $imagesFunctions = new Images();
                                            $timeStamps = $file->updated_at->getTimestamp();
                                            $mediaUrl = $url;
                                            $practice_info = [
                                                'practice_type' => $mediaFileType,
                                                'video_data' => $imagesFunctions->apiMediaModel($mediaUrl, $timeStamps, 2),
                                            ];
                                            break;
                                        case (3):
                                            $img = json_decode($file->url);
                                            $mediaData = null;
                                            if (isset($img)) {
                                                foreach ($img as $image) {
                                                    $mediaData [] = $image . "_$size.webp";
                                                }
                                            }
                                            $practiceTextParcer = new PracticeText();

                                            $practiceTextDataArray = $practiceTextParcer->PracticeTextPageGet(json_decode($file->descriptions), $file);

                                            $practice_info = [
                                                'practice_type' => $mediaFileType,
                                                'text_data' => $practiceTextDataArray
                                            ];
                                            break;
                                        default:
                                            break;
                                    }
                                } else {
                                    $practice_info = [
                                        'practice_type' => 0,
//                                        'practice_additional_data' => null,
                                    ];
                                }
                                $practiceFrames = [
                                    'practiceFrames' => $this->practiceFramesBuildData($gamesArr, $learning),
                                    'descriptions' => $descriptions,
                                    'tasks' => $tasksArray[$practiceGamesIncriment],
                                    'practice_info' => $practice_info
                                ];
                                foreach ($values as $key => $item) {
                                    $practiceFrames[$keys[$key]] = $item;
                                }
                                $practiceArray [] = $practiceFrames;

                            }
                        }
                        $data['practice'] = $practiceArray ?? [];
                    } else {
                        $practiceFrames = [
                            'practiceFrames' => [],
                            'descriptions' => [],
                            'tasks' => [],
                            'practice_info' => [],
                        ];
                        $practiceArray [] = $practiceFrames;
                        $data['practice'] = $practiceArray ?? [];

                    }
                } else {
                    $data['practice'] = [];
                }
                if (isset($this->learningWithTheories)) {
                    $learning = $this->learningWithTheories;
                } else {
                    $learning = $this->learningWithTheories = Learning::with('theories', 'practices', 'games')->find($scene->learning_id);
                }

                $theory = [];
                if (isset($learning->theories)) {
                    $theory = $theoryFunctions->buildTheoryData($learning->theories, $size, $lcid, true);
                }
                if (count($theory) == 0) {
                    $theory = null;
                }
                if ($this->chapter->type == 1) {
                    $this->otherElementsCheck($scene->other);
                }

                $data = $this->getArr1($id, $data, $infoData, $summaryData, $introductionData, $this->chapter, $motivationData);
                $data['theory'] = $theory ?? null;
                if ($this->chapter->type <> 2) {
                    if ($this->chapter->type == 1) {
                        if ($this->welcome) {
                            $data['welcome'] = ['frames' => $welcomeData];
                        } else {
                            $data['welcome'] = null;
                        }
                    } else {
                        $data['welcome'] = ['frames' => $welcomeData];
                    }
                } else {
                    $data['welcome'] = null;
                }
                $data['dialogue'] = $dialogueData;

            } else {
                $data = $this->getArr1($id, $data, $infoData, $summaryData, $introductionData, $this->chapter, $motivationData);
                $data['theory'] = null;
                if ($this->chapter->type <> 2) {
                    if ($this->chapter->type == 1) {
                        if ($this->welcome) {
                            $data['welcome'] = ['frames' => $welcomeData];
                        } else {
                            $data['welcome'] = null;
                        }
                    } else {
                        $data['welcome'] = ['frames' => $welcomeData];
                    }
                } else {
                    $data['welcome'] = null;
                }
                $data['practice'] = $practiceArray ?? [];
                $data['dialogue'] = $dialogueData;

            }
            if ($short == 1) {
                $data['timer'] = $scene->timer ?? null;
            }
            return json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);
        } else {
            return response('something wrong', 400);
        }
    }

    private function getArr(int $id, array $data, array $infoData, ?array $summaryData, array $introductionData, $chapter): array
    {
        $data['id'] = $id;
        if ($chapter->type <> 1) {
            $data['info'] = [
                'frame' => $infoData
            ];
        } else {
            $data['info'] = null;
        }
        if (isset($summaryData) && !empty($summaryData)) {
            if ($chapter->type == 1) {
                $data['summary'] = null;
            } else {
                $data['summary'] = ['frame' => $summaryData];
            }
        } else {
            $data['summary'] = null;
        }
        if ($chapter->type <> 2) {
            if ($chapter->type == 1) {
                if ($this->introduction) {
                    $data['introduction'] = ['frames' => $introductionData];
                } else {
                    $data['introduction'] = null;
                }
            } else {
                $data['introduction'] = ['frames' => $introductionData];
            }
        } else {
            $data['introduction'] = null;

        }
        return $data;

    }

    private function getArr1(int $id, array $data, array $infoData, ?array $summaryData, array $introductionData, $chapter, array $motivationData): array
    {
        $data = $this->getArr($id, $data, $infoData, $summaryData, $introductionData, $chapter);
        if ($chapter->type == 1) {
            if ($this->motivation) {
                $data['motivation'] = ['frames' => $motivationData];
            } else {
                $data['motivation'] = null;
            }
        } else {
            $data['motivation'] = null;
        }
        return $data;
    }
}
