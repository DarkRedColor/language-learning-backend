<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\GameTemplate;
use App\Models\GameWord;
use App\Models\Word;
use Illuminate\Support\Str;
use Exception;

class Practice extends Controller
{
    public function getType($type)
    {
//        'erudite' => 0,
//            'millionaire' => 1,
//            'linkWords' => 2,
//            'typeAnswer' => 3,
//            'makeSentence' => 4,
//            'insertWords' => 5,
//            'typeWords' => 6,
//            'insertSymbols' => 7,
//            'insertNumbers' => 8,
//            'trueOrFalse' => 9,
//            'removeWords' => 10,
        $typeArray = [
            'eruditeGames' => 0,
            'millionaireGames' => 1,
            'linkWordsGames' => 2,
            'typeAnswerGames' => 3,
            'makeASentenceGames' => 4,
            'insertWordsGames' => 5,
            'typeWordsGames' => 6,
            'trueOrFalseGames' => 9,
            'insertSymbolsGames' => 7,
            'insertNumbersGames' => 8,
            'removeWordsGames' => 10,
        ];

        return $typeArray[$type] ?? null;

    }

    private function theoryBuild($theory, $lcid = 'en')
    {
        $linked_theory = [];
        $isoTheoryTitle = 'description_' . $lcid;

        if (isset($theory)) {
            foreach ($theory as $item) {
                try {
                    $theoryId = $item->id;
                    $theoryType = 0;
                    if ($item->type == 2) {
                        $theoryType = 1;
                    }
                    if ($item->type == 1) {
                        $theoryType = 2;
                    }
                    $linked_theory [] =
                        [
                            'id' => $theoryId,
                            'type' => $theoryType,
                            'title' => $item->$isoTheoryTitle
                        ];
                } catch (Exception $e) {
                    $quizTheory = $item[0];
                    $theoryId = $quizTheory->id;
                    $theoryType = 0;
                    if ($quizTheory->type == 2) {
                        $theoryType = 1;
                    }
                    if ($quizTheory->type == 1) {
                        $theoryType = 2;
                    }
                    $linked_theory [] =
                        [
                            'id' => $theoryId,
                            'type' => $theoryType,
                            'title' => $quizTheory->$isoTheoryTitle

                        ];
                }
            }
        }
        return $linked_theory;
    }

    private function wordTheoryBuild($id)
    {


        $words = GameWord::where('game_id', $id)->where('type', '!=', 0)->where('type', '!=', 1)->get();

        foreach ($words as $item) {
            $linked_theory [] = [
                'id' => $item->word_id,
                'type' => 0,
                'title' => null,
            ];
        }

        return $linked_theory ?? [];

    }
    public function typeOfAdditionalTaskGet($gameTemplate)
    {
        switch ($gameTemplate->additional_task) {
            case ('audio'):
                $type = 2;
                break;
            case ('image'):
                $type = 1;
                break;
            default:
                $type = 0;
                break;
        }
        return $type;
    }

    public function typeOfTaskGet($gameTemplate)
    {
        $type = 0;
        switch ($gameTemplate->task) {
            case ('audio'):
                $type = 2;
                break;
            case ('image'):
                $type = 1;
                break;
            case ('symbols'):
            case ('space'):
                if ($gameTemplate->additional_task == 'audio') {
                    $type = 2;
                }
                break;
        }
        return $type;
    }

    public function typeGet($templateId, $gameTemplateArr)
    {
        $allTypes = [
            'erudite' => 0,
            'millionaire' => 1,
            'linkWords' => 2,
            'typeAnswer' => 3,
            'makeSentence' => 4,
            'insertWords' => 5,
            'typeWords' => 6,
            'insertSymbols' => 7,
            'insertNumbers' => 8,
            'trueOrFalse' => 9,
            'removeWords' => 10,

        ];
        if (array_key_exists($templateId, $gameTemplateArr)) {
            $gameTemplate = $gameTemplateArr[$templateId];
        } else {
            $gameTemplate = $gameTemplateArr[$templateId] = GameTemplate::find($templateId);
        }

        if (isset($gameTemplate)) {
            switch ($gameTemplate->answer) {
                case('3'):
                case ('4'):
                    if ($gameTemplate->task == 'link_words') {
                        $type = $allTypes['linkWords'];
                    } else {
                        $type = $allTypes['millionaire'];
                    }
                    break;
                case ('erudite'):
                    $type = $allTypes['erudite'];
                    break;
                case ('5'):
                    $type = $allTypes['linkWords'];
                    break;
                case ('type'):
                    if ($gameTemplate->task == 'space') {
                        $type = $allTypes['typeWords'];
                    } else {
                        $type = $allTypes['typeAnswer'];
                    }
                    break;
                case ('sentence'):
                    $type = $allTypes['makeSentence'];
                    break;
                case ('words'):
                    $type = $allTypes['insertWords'];
                    break;
                case ('true_false'):
                    $type = $allTypes['trueOrFalse'];
                    break;
                case('matched_not'):
                    $type = $allTypes['insertSymbols'];
                    break;
                case ('list'):
                    $type = $allTypes['insertNumbers'];
                    break;
                case ('remove'):
                    $type = $allTypes['removeWords'];
                    break;
                default:
                    $type = 9;
            }
        } else {
            $type = 55;
        }

        return [$type, $gameTemplateArr];
    }

    public function removeWordsDataBuild($game, $theories)
    {
        $id = $game->id;
        $theory = $theories;
        $linked_theory = $this->theoryBuild($theory);
        $choices = null;
        $additionalChoices = null;

        if (isset($game->answer)) {
            $answer = json_decode($game->answer);
            foreach ($answer as $key => $item) {
                if (in_array($key, json_decode($game->link_words))) {
                    $additionalChoices [] = $item;
                } else {
                    $choices [] = [
                        "displayed_value" => Str::lower($item),
                        "value" => $item,
                        'linked_theory' => $linked_theory,

                    ];
                }
            }
        }
        return [
            'id' => $id,
            'choices' => $choices,
            'additional_choices' => $additionalChoices,
        ];
    }

    public function typeWordsGameDataBuild($game, $theories)
    {

        $id = $game->id;
        $theory = $theories;
        $linked_theory = $this->theoryBuild($theory);
        $sentences = null;
        $clues = null;
        $answers = null;
        $data = null;

        $sentences = $this->getArr($game, $clues, $answers, (array)$data, (array)$sentences, $linked_theory);
        $options["considerCase"] = $this->trueFalseConvertor($game->capital);
        $options["considerPunctuation"] = $this->trueFalseConvertor($game->sign);
        return [
            'id' => $id,
            'options' => $options,
            'sentences' => $sentences,

        ];
    }

    public function insertWordsGameDataBuild($game, $theories)
    {
        $id = $game->id;
        $theory = $theories;
        $linked_theory = $this->theoryBuild($theory);
        $sentences = null;
        $clues = null;
        $answers = null;
        $additionalChoices = null;
        if (isset($game->mission_en)) {
            if (isset($game->link_words))
                $clues = json_decode($game->link_words);
            if (isset($game->answer))
                $answers = json_decode($game->answer);
            $mission = json_decode($game->mission_en);
            $i = 0;
            foreach ($mission as $key => $item) {
                $data = null;

                if (isset($item)) {
                    foreach (str_split($item) as $letter) {
                        if ($letter == "@") {
                            if (isset($answers) && is_array($answers)) {
                                if (isset($answers[$i]) && is_array($answers[$i])) {
                                    foreach ($answers[$i] as $answerValue) {
                                        $data[] = [
                                            'clue' => $clues[$key][$i] ?? null,
                                            'answer' => $answerValue ?? null,
                                            'linked_theory' => $linked_theory
                                        ];
                                    }
                                }
                            }
                            $i++;
                        }
                    }
                }
                if ($item == null) {
                    $sentences[] = [
                        'sentence' => $item,
                        'answers' => null
                    ];
                } else {
                    $sentences[] = [
                        'sentence' => $item,
                        'answers' => $data
                    ];
                }
            }
        }

        if (isset($game->wrong)) {
            $additionalChoices = json_decode($game->wrong);
        }

        $sentences = array_map(function ($sentence) {
            $sentence['sentence'] = preg_replace('#(</?strong>)#', '*', $sentence['sentence']);
            return $sentence;
        }, $sentences);

        return [
            'id' => $id,
            'sentences' => $sentences,
            'additional_choices' => $additionalChoices,
        ];

    }

    // ne prorabotanno textmodel (localizarile)
    public function makeASentenceGameDataBuild($game, $theories)
    {
        {
            $id = $game->id;
            $theory = $theories;
            $linked_theory = $this->theoryBuild($theory);
            $gameAnswer = null;
            $additionalChoices = null;

            if (isset($game->answer)) {
                $answer = json_decode($game->answer);
                foreach ($answer as $key => $item) {
                    if (in_array($key, json_decode($game->link_words))) {
                        $gameAnswer [] = [
                            "clue" => true,
                            "value" => $item,
                            'linked_theory' => $linked_theory,

                        ];
                    } else {
                        $gameAnswer [] = [
                            "clue" => false,
                            "value" => $item,
                            'linked_theory' => $linked_theory,

                        ];
                    }
                }
            }
            if (isset($game->wrong)) {
                $answer = json_decode($game->wrong);
                foreach ($answer as $item) {
                    $additionalChoices [] = $item;
                }
            }

            return [
                'id' => $id,
                'choices' => $gameAnswer,
                'mission' => null,
                'additional_choices' => $additionalChoices,
            ];
        }
    }

    private function trueFalseConvertor($item)
    {
        if ($item == 0) {
            return false;
        }
        return true;

    }

    // ne prorabotanno textmodel (localizarile)
    public function typeAnswerGameDataBuild($game, $theories, $lcid)
    {
        $id = $game->id;
        $theory = $theories;
        $linked_theory = $this->theoryBuild($theory);
        if ($game->input_type == 1) {
            $linked_theory = $this->wordTheoryBuild($game->id);
        }
        $gameAnswers = null;
        $lang = $lcid;
        $mission = null;
//        $gameTemplate = $game->gamesTemplate[0];

        $field = "link_words";

        if ($lcid != "en") {
            $field = "linked_words_$lcid";
        }
        if (is_array(json_decode($game->$field))) {
            $field = "link_words";
            $lang = 'en';
        } elseif (json_decode($game->$field) == null) {
            $field = "link_words";
            $lang = 'en';
        }
        if ($game->input_type == 0) {
            if (isset($game->$field)) {
                $answer = json_decode($game->$field);
                if (isset($answer)) {
                    foreach ($answer as $item) {
                        $gameAnswer = null;
                        if ($lang == "en") {
                            $answerAndClue = explode('{', $item);
                            if (is_array($answerAndClue)) {
                                foreach ($answerAndClue as $value) {
                                    if (str_contains($value, '}')) {
                                        $gameAnswer['clue'] = str_replace('}', '', $value);
                                    } else {
                                        $gameAnswer['value'] = $value;
                                    }
                                }
                            } else {
                                $gameAnswer['value'] = $answerAndClue;
                            }

                            if (str_contains($gameAnswer['value'], '/')) {
                                $gameAnswer['value'] = explode('/', $gameAnswer['value']);
                                $gameAnswers[] = [
                                    'value' => $gameAnswer['value'] ?? null,
                                    'clue' => $gameAnswer['clue'] ?? null,
                                    'linked_theory' => $linked_theory,
                                ];
                            } else {
                                $gameAnswers[] = [
                                    'value' => [$gameAnswer['value']] ?? null,
                                    'clue' => $gameAnswer['clue'] ?? null,
                                    'linked_theory' => $linked_theory,
                                ];
                            }
                        } else {
                            if (isset($item)) {
                                if (str_contains($item, '/')) {
                                    $item = explode('/', $item);
                                    $gameAnswers[] = [
                                        'value' => $item ?? null,
                                        'clue' => null,
                                        'linked_theory' => $linked_theory,
                                    ];
                                } else {
                                    $gameAnswers[] = [
                                        'value' => [$item] ?? null,
                                        'clue' => null,
                                        'linked_theory' => $linked_theory,
                                    ];
                                }
                            }
                        }
                    }
                }
            }
            if ($game->gamesTemplate[0]->additional_task == 'image') {
                if (isset($game->theory))
                    $mission = json_decode($game->theory)[0] ?? null;
            } else {
                $mission = $game->description_en ?? null;
            }
        } else {

            $answer = Word::find(GameWord::where('game_id', $game->id)->where('type', 2)->first()->word_id) ?? null;
            $gameAnswers [] = [
                'linked_theory' => $linked_theory,
                'clue' => null,
                'value' => [$answer->word],
            ];

        }

        $options["considerCase"] = $this->trueFalseConvertor($game->capital);
        $options["considerPunctuation"] = $this->trueFalseConvertor($game->sign);
        return [
            'id' => $id,
            'options' => $options,
            'mission' => $mission,
            'answer' => $gameAnswers,
        ];
    }


    public function linkWordsGameDataBuild($game, $theories, $lcid, $gameTemplateArr = [])
    {
        $id = $game->id;
        $theory = $theories;
        $linked_theory = $this->theoryBuild($theory);
        $gameAnswer = null;
        $type = 0;
        $localisedField = 'word';
        if ($lcid <> "en") {
            $localisedField = "meaning_$lcid";
        }
        if (array_key_exists($game->gamesTemplate[0]->id, $gameTemplateArr)) {
            $gameTemplate = $gameTemplateArr[$game->gamesTemplate[0]->id];
        } else {
            $gameTemplate = $gameTemplateArr[$game->gamesTemplate[0]->id] = GameTemplate::find($game->gamesTemplate[0]->id);
        }
        if ($gameTemplate->additional_task == 'audio') {
            $type = 1;
        }

        if ($game->input_type == 0) {

            if (isset($game->answer)) {
                $answer = json_decode($game->answer);
                $size = count($answer);
                $aux = $size / 2;


                foreach ($answer as $key => $value) {
                    if ($key == $aux) {
                        break;
                    }

                    if (is_array($value)) {
                        foreach ($value as $item) {
                            if (isset($item))
                                $gameAnswer [] = [
                                    'left' => [
                                        "value" => $item,
                                        "linked_theory" => $linked_theory,
                                    ],
                                    'right' => [
                                        "value" => $answer[$key + $aux][0],
                                        "linked_theory" => $linked_theory,
                                    ]
                                ];
                        }
                    } else {
                        if (isset($value))
                            $gameAnswer [] = [
                                'left' => [
                                    "value" => $value,
                                    "linked_theory" => $linked_theory,
                                ],
                                'right' => [
                                    "value" => $answer[$key + $aux],
                                    "linked_theory" => $linked_theory,
                                ],
                            ];
                    }
                }
            }
        } else {
            $words = GameWord::where('game_id', $game->id)
                ->where('type', 4)
                ->where('link_words', null)
                ->get();
            $linked_words = GameWord::where('game_id', $game->id)
                ->where('type', 4)
                ->whereNotNull('link_words')
                ->get();
            if ($type == 1) {
                $words = GameWord::where('game_id', $game->id)
                    ->where('type', 6)
                    ->where('link_words', null)
                    ->get();
                $linked_words = GameWord::where('game_id', $game->id)
                    ->where('type', 6)
                    ->whereNotNull('link_words')
                    ->get();
            }
            foreach ($words as $item) {
                if ($linked_words->where('link_words', $item->id)->first() !== null) {
                    $localised = $linked_words->where('link_words', $item->id)->first()->localised;
                    if ($localised == 0) {
                        $gameAnswer [] = [
                            'left' => [
                                "value" => Word::find($item->word_id)->word,
                                "linked_theory" => [
                                    [
                                        'type' => 0,
                                        'id' => $item->word_id
                                    ]
                                ],
                            ],
                            'right' => [
                                "value" => Word::find($linked_words->where('link_words', $item->id)->first()->word_id)->word,
                                "linked_theory" => [
                                    [
                                        'type' => 0,
                                        'id' => $linked_words->where('link_words', $item->id)->first()->word_id
                                    ]
                                ],
                            ],
                        ];
                    } else {
                        $gameAnswer [] = [
                            'left' => [
                                "value" => Word::find($item->word_id)->word,
                                "linked_theory" => [
                                    [
                                        'type' => 0,
                                        'id' => $item->word_id
                                    ]
                                ],
                            ],
                            'right' => [
                                "value" => Word::find($linked_words->where('link_words', $item->id)->first()->word_id)->$localisedField,
                                "linked_theory" => [
                                    [
                                        'type' => 0,
                                        'id' => $linked_words->where('link_words', $item->id)->first()->word_id
                                    ]
                                ],
                            ],
                        ];
                    }
                }

            }

        }

        return [[
            'id' => $id,
            'type' => $type,
            'data' => $gameAnswer,
        ],$gameTemplateArr];

    }

    public function insertSymbolsDataBuild($game, $theories)
    {
        $id = $game->id;
        $theory = $theories;
        $linked_theory = $this->theoryBuild($theory);
        $options = null;
        $mission = null;
        $data = json_decode($game->link_words);
        if (isset($data))
            foreach ($data as $item) {
                $value = $item[0];
                if ($item[0] == 'T') {
                    $value = 0;
                } elseif ($item[0] == 'F') {
                    $value = -1;
                }
                $mission [] = [
                    'sentence' => $item[1],
                    'value' => $value + 1,
                    'linked_theory' => $linked_theory
                ];
            }
        else {
            $mission [] = [
                'sentence' => null,
                'value' => null,
                'linked_theory' => $linked_theory
            ];
        }
        return [
            'id' => $id,
            'options' => $options,
            'mission' => $mission
        ];
    }

    public function trueOrFalseDataBuild($game, $theories)
    {
        $id = $game->id;
        $theory = $theories;
        $linked_theory = $this->theoryBuild($theory);
        $gameAnswer = null;
        $correctIndex = 1;
        $options = [
            "considerCase" => 0,
            "considerPunctuation" => 0
        ];
        $mission = null;
        $answer = null;

        if (isset($game->answer)) {
            $answer = json_decode($game->answer);
            if (isset($game->mission_en)) {
                $mission = json_decode($game->mission_en);
                if ($answer == $mission) {
                    $gameAnswer = [
                        "no",
                        "yes",
                    ];
                } else {
                    $correctIndex = 0;
                    $gameAnswer = [
                        "no",
                        "yes",
                    ];
                }
            }
        }
        return [
            'id' => $id,
            'linked_theory' => $linked_theory,
            'options' => $options,
            'correctIndex' => $correctIndex,
            'choices' => $gameAnswer,
            'mission' => [
                'value' => $mission[0] ?? null,
                'clue' => null,

            ],
            'answer' => $answer[0] ?? null
        ];
    }

    public function millionaireGameDataBuild($game, $theories, $size, $lcid, $randomLocalise)
    {
        $id = $game->id;
        $taskType = $game->gamesTemplate[0]->task;
        $theory = $theories;
        $linked_theory = $this->theoryBuild($theory);
        $mission = null;
        $gameAnswer = null;
        $type = 1;
        $correctIndex = 0;
        $clue = null;
        $correctAnswer = null;
        $localisedField = "meaning_$lcid";


        if ($game->gamesTemplate[0]->answer == '4') {
            $type = 0;
        }

        if ($game->input_type == 0) {
            if (isset($game->answer)) {
                $answer = json_decode($game->answer);
                foreach ($answer as $value) {
                    if (is_array($value)) {
                        foreach ($value as $item) {
                            $gameAnswer [] = [
                                'text' => $item,
                                'linked_theory' => $linked_theory
                            ];
                        }
                    } else {
                        $gameAnswer [] = [
                            'text' => $value,
                            'linked_theory' => $linked_theory
                        ];;

                    }
                }
                if (isset($game->wrong)) {
                    $wrong = json_decode($game->wrong);
                    foreach ($wrong as $value) {
                        if (isset($value)) {
                            $gameAnswer [] = [
                                'text' => $value,
                                'linked_theory' => $linked_theory
                            ];;
                        }
                    }
                }
            }
            if (isset($game->link_words)) {
                $clue = json_decode($game->link_words);
            }
        } else {

            $words = GameWord::where('game_id', $game->id)
                ->where('type', '!=', 0)
                ->where('type', '!=', 1)
                ->where('type', '!=', 2)
                ->get();
            $correctAnswer = GameWord::where('game_id', $game->id)
                ->where('type', 2)
                ->first();
            if (isset($correctAnswer)) {
                if (Word::find($correctAnswer->word_id) !== null) {
                    if ($randomLocalise == 0) {
                        $gameAnswer[] = [
                            'text' => Word::find($correctAnswer->word_id)->word,
                            'linked_theory' => [
                                [
                                    'id' => $correctAnswer->word_id,
                                    'type' => 0
                                ]
                            ]
                        ];
                    } else {
                        if ($taskType <> 'image')
                            $gameAnswer[] = [
                                'text' => Word::find($correctAnswer->word_id)->$localisedField,
                                'linked_theory' => [
                                    [
                                        'id' => $correctAnswer->word_id,
                                        'type' => 0
                                    ]
                                ]
                            ];
                        else
                            $gameAnswer[] = [
                                'text' => Word::find($correctAnswer->word_id)->word,
                                'linked_theory' => [
                                    [
                                        'id' => $correctAnswer->word_id,
                                        'type' => 0
                                    ]
                                ]
                            ];
                    }
                } else {
                    $gameAnswer[] = [
                        'text' => null,
                        'linked_theory' => [
                            [
                                'id' => $correctAnswer->word_id,
                                'type' => 0
                            ]
                        ]
                    ];
                }
            } else {
                $gameAnswer[] = [
                    'text' => null,
                    'linked_theory' => [
                        [
                            'id' => null,
                            'type' => 0
                        ]
                    ]
                ];
            }
            foreach ($words as $item) {
                if ($randomLocalise == 0) {
                    $gameAnswer[] = [
                        'text' => Word::find($item->word_id)->word,
                        'linked_theory' => [
                            [
                                'id' => $item->word_id,
                                'type' => 0
                            ]
                        ]
                    ];
                } else {
                    if ($taskType <> 'image')

                        $gameAnswer[] = [
                            'text' => Word::find($item->word_id)->$localisedField,
                            'linked_theory' => [
                                [
                                    'id' => $item->word_id,
                                    'type' => 0
                                ]
                            ]
                        ];
                    else
                        $gameAnswer[] = [
                            'text' => Word::find($item->word_id)->word,
                            'linked_theory' => [
                                [
                                    'id' => $item->word_id,
                                    'type' => 0
                                ]
                            ]
                        ];
                }
            }

        }

        if (isset($game->mission_en)) {
            if ($taskType == 'text') {
                if ($randomLocalise == 0) {
                    if ($game->input_type == 1)
                        if ($taskType <> 'image')

                            $mission[] = [
                                'text' => Word::find($correctAnswer->word_id)->$localisedField,
                                'linked_theory' => [
                                    [
                                        'id' => $correctAnswer->word_id,
                                        'type' => 0
                                    ]
                                ]
                            ];
                        else
                            $mission[] = [
                                'text' => Word::find($correctAnswer->word_id)->word,
                                'linked_theory' => [
                                    [
                                        'id' => $correctAnswer->word_id,
                                        'type' => 0
                                    ]
                                ]
                            ];
                } else {
                    if (json_decode($game->mission_en) !== null)
                        foreach (json_decode($game->mission_en) as $item) {
                            if (!isset($item)) {
                                $mission = null;
                            }
//                            else {
//                                $mission[] = [
//                                    'sentence' => $item ?? null,
//                                    'clue' => null,
//                                ];
//                            }
                        }

                }
            } elseif ($taskType == 'space') {
                if (json_decode($game->mission_en) !== null)
                    foreach (json_decode($game->mission_en) as $key => $item) {

                        $mission[] = [
                            'sentence' => str_replace(["<strong>", "</strong>"], '*', str_replace(" $", '', $item ?? null)),
                            'clue' => $clue[$key][0] ?? null,
                        ];
                        if (!isset($item)) {
                            $mission = null;
                        }
                    }
            }
        }

        return [
            'id' => $id,
            'type' => $type,
            'correctIndex' => $correctIndex,
            'choices' => $gameAnswer,
            'mission' => $mission,
        ];


    }

    public function eruditeGameDataBuild($game, $theories)
    {
        $id = $game->id;
        $theory = $theories;
        $linked_theory = $this->theoryBuild($theory);
        if ($game->input_type == 1) {
            $linked_theory = $this->wordTheoryBuild($game->id);
        };
        $mission = null;
        $gameAnswer = null;

        if (isset($game->answer)) {
            $answer = json_decode($game->answer);
            foreach ($answer as $key => $value) {
                $gameAnswer .= $value;
                if (isset($game->link_words)) {
                    if (json_decode($game->link_words) <> null)
                        if (in_array($key, json_decode($game->link_words))) {
                            $mission .= $value;
                        } else {
                            $mission .= '@';
                        }
                } else {
                    $mission .= '@';
                }
            }
        }
        $i = 0;
        foreach (str_split($mission) as $letter) {
            if ($letter <> "@") {
                $i++;
            }
        }
        if ($i == 0) {
            $mission = null;
        }


        return [
            'id' => $id,
            'linked_theory' => $linked_theory,
            'mission' => $mission,
            'answer' => $gameAnswer,
        ];
    }

    private function getArr($game, $clues, $answers, array $data, array $sentences, $linked_theory): array
    {
        if (isset($game->mission_en)) {
            if (isset($game->link_words))
                $clues = json_decode($game->link_words);
            if (isset($game->answer))
                $answers = json_decode($game->answer);
            $mission = json_decode($game->mission_en);
            $i = 0;
            foreach ($mission as $key => $item) {
                if (isset($item)) {
                    $answer = null;
                    foreach (str_split($item) as $letter) {
                        if ($letter == "@") {
                            if (isset($answers) && is_array($answers)) {
                                if (isset($answers[$i]) && is_array($answers[$i])) {
                                    foreach ($answers[$i] as $answerValue) {
                                        $answer[] = $answerValue;
                                    }
                                }
                            }
                            $i++;
                        }
                    }
                }
                if (isset($clues[$key]) && !empty($clues[$key]))
                    foreach ($clues[$key] as $value) {
                        $clue = $value ?? null;

                        $data = [
                            'clue' => $clue,
                            'answers' => $answer ?? null,
                            'linked_theory' => $linked_theory
                        ];
                    }
                else {
                    $data = [
                        'clue' => null,
                        'answers' => $answer ?? null,
                        'linked_theory' => $linked_theory
                    ];
                }
                if ($data['answers'] == null) {
                    $sentences[] = [
                        'sentence' => str_replace(['$ ', "$"], '', $item),
                        'answers' => null
                    ];
                } else {
                    $sentences[] = [
                        'sentence' => str_replace(['$ ', "$"], '', $item),
                        'answers' => [$data]
                    ];
                }
            }
        }

        $sentences = array_map(function ($sentence) {
            $sentence['sentence'] = preg_replace('#(</?strong>)#', '*', $sentence['sentence']);
            return $sentence;
        }, $sentences);

        return $sentences;
    }

}
