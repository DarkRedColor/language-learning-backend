<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\GameDescription;
use App\Models\GameTemplate;
use App\Models\GameWord;
use App\Models\Learning;
use App\Models\Practice;
use App\Models\Theory;
use App\Models\Word;
use Illuminate\Http\Request;

class GamesGenerate extends Controller
{

    private function buildAllItemsFromCollectionsToOneArray($collection)
    {
        $arr = null;
        if (isset($collection) && !empty($collection)) {
            foreach ($collection as $array) {
                foreach ($array as $item) {
                    $arr [] = $item;
                }
            }
        }
        return $arr;
    }

    private function getAllWordsOfTheory($theories)
    {
        $arr = [];
        foreach ($theories as $item) {
            if ($item->type == 0) {
                $arr[] = $item->id;
            }
        }
        $collectionsOfWords = null;
        foreach ($arr as $item) {
            $collectionsOfWords [] = Theory::with('words')->find($item)->words;
        }
        return $this->buildAllItemsFromCollectionsToOneArray($collectionsOfWords);
    }

    private function getAllGameTemplatesOfSelectedPractices($practices)
    {
        $arr = [];
        foreach ($practices as $item) {
            $arr[] = $item->id;
        }

        $collectionsOfGameTemplates = null;
        foreach ($arr as $item) {
            $collectionsOfGameTemplates[] = Practice::with('gameTemplates')->find($item)->gameTemplates;
        }

        return $this->buildAllItemsFromCollectionsToOneArray($collectionsOfGameTemplates);

    }

    private function getIndex($collections, $i)
    {
        $tempCount = $i;
        if (isset($collections)) {
            if (count($collections) <= $i) {
                $tempCount = $i % count($collections);
            }
        }

        return $tempCount;

    }

    private function taskCreate($word, $gameTemplate)
    {
        $task = null;
        if (!in_array($gameTemplate->task, ['link_words', 'space']) || !in_array($gameTemplate->answer, ['sentence', 'words', 'remove', 'true_false'])) {
            switch ($gameTemplate->task) {
                case('image'):
                    $task = [$word->image];
                    break;
                case ('audio'):
                case ('text'):
                    switch ($gameTemplate->answer) {
                        case ("type"):
                        case ('3'):
                        case ('4'):
                        case ('erudite'):
                            $task = $word->id;
                            break;
                        default:
                            $task = [$word->word];
                            if ($gameTemplate->answer == 'erudite') {
                                $task = $word->word;
                            }
                            break;
                    }
                    break;
                default:
                    break;
            }
        }
        return $task;
    }

    private function answerCreate($word, $gameTemplate, $wordsList)
    {

        $answer = null;
        if (!in_array($gameTemplate->task, ['link_words', 'space']) || !in_array($gameTemplate->answer, ['sentence', 'words', 'remove', 'true_false'])) {
            switch ($gameTemplate->answer) {
                case ('3'):
                    if (!in_array($gameTemplate->task, ['link_words', 'space'])) {
                        $answer = $word->id;
                    }
                    break;
                case ('4'):
                    if ($gameTemplate->task == 'link_words') {
                        $answer = [
                            $word->word,
                            $wordsList[array_rand($wordsList)]->word,
                            $wordsList[array_rand($wordsList)]->word,
                            $wordsList[array_rand($wordsList)]->word
                        ];
                    } elseif ($gameTemplate->task <> 'space') {
                        $answer = $word->id;
                    }
                    break;
                case ('5'):
                    if ($gameTemplate->task == 'link_words') {
                        $answer = [
                            $word->word,
                            $wordsList[array_rand($wordsList)]->word,
                            $wordsList[array_rand($wordsList)]->word,
                            $wordsList[array_rand($wordsList)]->word,
                            $wordsList[array_rand($wordsList)]->word,
                        ];
                    }
                    break;
                case ('erudite'):
                    if ($gameTemplate->task <> 'space') {
                        $splitWord = str_split($word->word);
                        if (count($splitWord) <= 7) {
                            $answer = $word->id;
                        } elseif ($splitWord[6] == ' ' && count($splitWord) < 15) {
                            $answer = $word->id;
                        }
                    }
                    break;
                case ('type'):
                    $answer = $word->id;
                    break;
                default:
                    break;
            }
        }
        return $answer;
    }

    private function wrongAnswerCreate($gameTemplate, $wordsList)
    {
        $wrong = null;
        switch ($gameTemplate->answer) {
            case ('3'):
                if (!in_array($gameTemplate->task, ['link_words', 'space'])) {
                    $firstWrongIndex = array_rand($wordsList);
                    $firstWrong = $wordsList[$firstWrongIndex]->id;
                    unset($wordsList[$firstWrongIndex]);
                    $secondWrong = $wordsList[array_rand($wordsList)]->id;
                    $wrong = [
                        $firstWrong,
                        $secondWrong,
                    ];
                }
                break;
            case ('4'):
                if (!in_array($gameTemplate->task, ['link_words', 'space'])) {
                    $firstWrongIndex = array_rand($wordsList);
                    $firstWrong = $wordsList[$firstWrongIndex]->id;
                    unset($wordsList[$firstWrongIndex]);
                    $secondWrongIndex = array_rand($wordsList);
                    $secondWrong = $wordsList[$secondWrongIndex]->id;
                    unset($wordsList[$secondWrongIndex]);
                    $thirdWrong = $wordsList[array_rand($wordsList)]->id;
                    $wrong = [
                        $firstWrong,
                        $secondWrong,
                        $thirdWrong
                    ];
                }
                break;
            default:
                break;
        }

        return $wrong;
    }

    private function additionalTaskCreate($gameTemplate, $word)
    {
        $additionalTask = null;
        if (!in_array($gameTemplate->task, ['link_words', 'space']) || !in_array($gameTemplate->answer, ['sentence', 'words', 'remove', 'true_false'])) {
            switch ($gameTemplate->additional_task) {
                case('image'):
                    $additionalTask = [$word->image];
                    break;
                case ('text'):
                case ('audio'):
                    $additionalTask = [$word->word];
                    break;
                default:
                    break;
            }
        }

        return $additionalTask;
    }

    private function gameCreation($i, $collectionsOfWords, $collectionsOfGameTemplates, $id)
    {
        $index = $this->getIndex($collectionsOfWords, $i);
        $word = $collectionsOfWords[$index];

        $gameTemplate = $collectionsOfGameTemplates;

        $task = json_encode($this->taskCreate($word, $gameTemplate));

        $answer = json_encode($this->answerCreate($word, $gameTemplate, array_diff($collectionsOfWords, [$word])));

        $additionalTask = json_encode($this->additionalTaskCreate($gameTemplate, $word));

        $wrong = json_encode($this->wrongAnswerCreate($gameTemplate, array_diff($collectionsOfWords, [$word])));

        if (!in_array($gameTemplate->task, ['link_words', 'space'])) {
            if (!in_array($gameTemplate->answer, ['sentence', 'words', 'remove', 'true_false'])) {
                if ($gameTemplate->additional_task == 'image') {
                    $game = Game::create([
                        'mission_en' => $task,
                        'answer' => $answer,
                        'wrong' => $wrong,
                        'theory' => $additionalTask,
                    ]);
                } else {
                    $game = null;
                    if ((in_array($gameTemplate->task, ['text', 'audio', 'image'])) && (in_array($gameTemplate->answer, ["3", '4', 'type', 'erudite']))) {
                        switch ($gameTemplate->answer) {
                            case ("3"):
                            case ("4"):
                                $game = $this->getGame($gameTemplate, $task, $answer);
                                foreach (json_decode($wrong) as $item) {
                                    GameWord::create([
                                        'game_id' => $game->id,
                                        'word_id' => $item,
                                        'type' => 3
                                    ]);
                                }
                                break;
                            case ("type"):
                                $game = $this->getGame($gameTemplate, $task, $answer);
                                break;
                            case ("erudite"):
                                if ($answer <> null && $answer <> "null") {
                                    switch ($gameTemplate->task) {
                                        case ('text'):
                                            $type = 1;
                                            break;
                                        case('image'):
                                            $type = 'image';
                                            break;
                                        default:
                                            $type = 0;
                                            break;
                                    }
                                    if ($type <> 'image') {
                                        $game = Game::create([
                                            'input_type' => 1,
                                        ]);
                                        $splitWord = str_split(Word::find($task)->word);
                                        if (count($splitWord) <= 7) {
                                            GameWord::create([
                                                'game_id' => $game->id,
                                                'word_id' => json_decode($task),
                                                'type' => $type
                                            ]);
                                            Game::find($game->id)->update([
                                                'answer' => $splitWord,
                                            ]);
                                        } elseif ($splitWord[6] == ' ' && count($splitWord) < 15) {
                                            GameWord::create([
                                                'game_id' => $game->id,
                                                'word_id' => json_decode($task),
                                                'type' => $type
                                            ]);
                                            Game::find($game->id)->update([
                                                'answer' => $splitWord,
                                            ]);
                                        }
                                    } else {
                                        $wordCollection = Word::find($answer);
                                        $answer = $wordCollection->word;
                                        $splitWord = str_split($answer);
                                        if (count($splitWord) <= 7) {
                                            $game = Game::create([
                                                'input_type' => 1,
                                                'mission_en' => $task
                                            ]);
                                            GameWord::create([
                                                'game_id' => $game->id,
                                                'word_id' => $wordCollection->id,
                                                'type' => 4
                                            ]);
                                            Game::find($game->id)->update([
                                                'answer' => $splitWord,
                                            ]);

                                        } elseif ($splitWord[6] == ' ' && count($splitWord) < 15) {
                                            $game = Game::create([
                                                'input_type' => 1,
                                                'mission_en' => $task
                                            ]);
                                            Game::find($game->id)->update([
                                                'answer' => $splitWord,
                                            ]);
                                        }
                                    }
                                    if (json_decode($answer) !== null)
                                        GameWord::create([
                                            'game_id' => $game->id,
                                            'word_id' => $wordCollection->id,
                                            'type' => 2
                                        ]);
                                    else
                                        GameWord::create([
                                            'game_id' => $game->id,
                                            'word_id' => $wordCollection->id,
                                            'type' => 2
                                        ]);
                                }
                                break;
                            default:
                                break;
                        }
                    } else {
                        $game = Game::create([
                            'mission_en' => $task,
                            'answer' => $answer,
                            'wrong' => $wrong,
                            'link_words' => $additionalTask,
                        ]);
                    }

                }
                if ($game <> null) {
                    GameTemplate::find($gameTemplate->id)->games()->attach($game->id);
                    Practice::find($id->practices[0]->id)->games()->attach($game->id);
                    Learning::find($id->id)->games()->attach($game->id);
                }
            }
        }
    }

    public function gamesAutoGenerate($allLearningCollectionWithRelations, $count = 0)
    {
        $collectionsOfWords = $this->getAllWordsOfTheory($allLearningCollectionWithRelations->theories);
        if (isset($collectionsOfWords))
            shuffle($collectionsOfWords);

        $collectionsOfGameTemplates = $this->getAllGameTemplatesOfSelectedPractices($allLearningCollectionWithRelations->practices);
        if (isset($collectionsOfGameTemplates)) {
            shuffle($collectionsOfGameTemplates);
            foreach ($collectionsOfGameTemplates as $item) {
                $i = 0;
                while ($count > $i) {
                    $this->gameCreation($i, $collectionsOfWords, $item, $allLearningCollectionWithRelations);
                    $i++;
                }
            }
        }

    }

    private function getGame($gameTemplate, $task, $answer): Game
    {
        switch ($gameTemplate->task) {
            case ('text'):
                $type = 1;
                break;
            case('image'):
                $type = 'image';
                break;
            default:
                $type = 0;
                break;
        }
        if ($type <> 'image') {
            $game = Game::create([
                'input_type' => 1,
            ]);
            GameWord::create([
                'game_id' => $game->id,
                'word_id' => json_decode($task),
                'type' => $type
            ]);
        } else {
            $game = Game::create([
                'input_type' => 1,
                'mission_en' => $task
            ]);
        }
        GameWord::create([
            'game_id' => $game->id,
            'word_id' => json_decode($answer),
            'type' => 2
        ]);
        GameWord::create([
            'game_id' => $game->id,
            'word_id' => json_decode($answer),
            'type' => 4
        ]);
        return $game;
    }
}