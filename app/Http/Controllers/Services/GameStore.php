<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\GameWord;
use Arr;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GameStore extends Controller
{
    private $audio = 0;

    public function GameDataStore($request, $templateId)
    {
        $gameTemplate = [];
        $practiceFunctions = new Practice();
        [$type, $gameTemplate] = $practiceFunctions->typeGet($templateId, $gameTemplate);

        switch ($type) {
            case (0):
                $game = $this->eruditeStore($request, current($gameTemplate));
                break;
            case (10):
                $game = $this->removeWordsStore($request, current($gameTemplate));
                break;
            case (2):
                $game = $this->linkWordsStore($request, current($gameTemplate));
                break;
            case (3):
                $game = $this->typeAnswerStore($request, current($gameTemplate));
                break;
            case (4):
                $game = $this->makeSentenceStore($request);
                break;
            case (5):
                $game = $this->insertWordsStore($request);
                break;
            case (6):
                $game = $this->typeWordsStore($request);
                break;
            case (7):
                $game = $this->insertSymbolsStore($request);
                break;
            case (8):
                $game = $this->insertNumbersStore($request);
                break;
            case (9):
                $game = $this->trueOrFalseStore($request);
                break;
            default:
                $game = $this->millionaireStore($request);
                break;
        }
        if ($request->hasFile('image')) {
            $imageFunctions = new Images();
            $file = $request->file('image');
            $imageFunctions->pngConvertAdventures($game->id, 'game',  $file,false);


        }

        if ($request->hasFile('image-additional')) {
            $imageFunctions = new Images();
            $file = $request->file('image-additional');
            $imageFunctions->pngConvertAdventures($game->id, 'game/additional',  $file,false);

        }


        if ($request->hasFile('audio')) {
            $apiRequest = new ApiRequest();
            $audio = $request->file('audio');
            $nameWord = str_replace(' ', '_', $game->id . '.mp3');
            $apiRequest->postSftpContent($audio, $nameWord, 'game/audio');
            Game::find($game->id)->update([
                'audio' => 1
            ]);
        }

        return $game;

    }


    private function removeWordsStore($request, $gameAnswerType)
    {
        return $this->eruditeStore($request, $gameAnswerType);
    }

    private function trueOrFalseStore($request)
    {
        return $this->millionaireStore($request);
    }

    private function insertNumbersStore($request)
    {
        return $this->insertSymbolsStore($request);
    }

    private function insertSymbolsStore($request)
    {
        $link_answers = [];
        foreach ($request['link_answers'] as $key => $item) {
            if (count(explode(',@@', $item)) >= 2) {
                $link_answers[] = explode(',@@', $item);
            } else {
                $link_answers[] = [
                    $key, $item
                ];
            }
        }
        if (isset($request['audio'])) {
            $this->audio = 1;
        }
        $game = Game::create([
            'link_words' => json_encode($link_answers),
            'mission_en' => json_encode($request['task-text'] ?? $request['task-audio']),
            'game_description_id' => $request['description'],
            'audio' => $this->audio,
            'read_type' => $request['read_type'] ?? 0
        ]);

        return $game;
    }

    private function typeWordsStore($request)
    {
        return $this->insertWordsStore($request);
    }

    private function insertWordsStore($request)
    {
        $request = $this->spaceDataBuild($request);
        $readType = $request['read_type'] ?? 0;
        if (isset($request['audio'])) {
            $this->audio = 1;
        }
        $game = Game::create([
            'answer' => json_encode($request['correct_answer']),
            'mission_en' => json_encode($request['task-text'] ?? $request['task-audio']),
            'wrong' => json_encode($request['wrong-answer']),
            'link_words' => json_encode($request['additional_task']),
            'game_description_id' => $request['description'],
            'read_type' => $readType,
            'sign' => $request['sign'] ?? 0,
            'capital' => $request['capital'] ?? 0,
            'audio' => $this->audio
        ]);

        return $game;
    }

    private function makeSentenceStore($request)
    {
        $keyData = $this->gamesWithTagsProcessingFunction($request);
        $game = Game::create($keyData);

        return $game;
    }

    private function typeAnswerStore($request, $gameAnswerType)
    {
        if ($request['input_type'] == null || $request['input_type'] == 'manual') {
            $keyData = null;
            [$keys, $values] = Arr::divide($request->all());
            for ($i = 5; $i < count($keys); $i++) {
                $keyData[$keys[$i]] = $values[$i];
            }
            unset($keyData['audio']);
            if ($gameAnswerType->task == 'audio') {
                $mission_en = $request['task-text'] ?? $request['task-audio'];
                $mission_en = json_encode($mission_en);
                if (isset($request['audio'])) {
                    $this->audio = 1;
                }
                $game = Game::create([
                    'mission_en' => $mission_en,
                    'link_words' => json_encode($request['link_words']),
                    'game_description_id' => $request['description'],
                    'description_en' => $request['additional_task'][0] ?? null,
                    'audio' => $this->audio
                ]);
            } else {
                $game = Game::create([
                    'link_words' => json_encode($request['link_words']),
                    'game_description_id' => $request['description'],
                    'description_en' => $request['additional_task'][0] ?? null,
                    'audio' => $this->audio
                ]);
            }
            unset($keyData['link_words']);

            $keyData['input_type'] = 0;


            Game::find($game->id)->update($keyData);
        } else {

            $readType = $request['read_type'] ?? 0;

            if (isset($request['audio'])) {
                $this->audio = 1;
            }
            $game = Game::create([
                'game_description_id' => $request['description'],
                'input_type' => 1,
                'read_type' => $readType,
                'audio' => $this->audio
            ]);
            if ($gameAnswerType->task == 'text')
                GameWord::create([
                    'game_id' => $game->id,
                    'word_id' => $request['mission_en'],
                    'type' => 1,
                ]);
            elseif ($gameAnswerType->task == 'audio') {
                GameWord::create([
                    'game_id' => $game->id,
                    'word_id' => $request['task-audio'][0],
                    'type' => 0,
                ]);
            }
            GameWord::create([
                'game_id' => $game->id,
                'word_id' => $request['link_words'][0],
                'type' => 2,
            ]);
        }
        return $game;
    }

    private function millionaireStore($request)
    {
        if ($request['input_type'] == null || $request['input_type'] == 'manual') {
            $task = $request['task-text'] ?? $request['task-audio'];
            $answer = $request['correct_answer'];

            if (is_array($request['correct_answer']) || isset($request['task-audio'])) {
                $answer = json_encode($request['correct_answer']);
            }
            if (!isset($task)) {
                $request = $this->spaceDataBuild($request);
                $task = $request['task-text'] ?? $request['task-audio'];
                $answer = json_decode($request['answers']);
            }

            if (isset($request['audio'])) {
                $this->audio = 1;
            }
            $keyData = [];
            if ($request['hint_option'] <> 1) {

                [$keys, $values] = \Illuminate\Support\Arr::divide($request->all());
                foreach ($keys as $index => $key) {
                    if (Str::contains($key, 'hint_')) {
                        if ($key <> 'hint_option') {
                            $keyData [$keys[$index]] = $values[$index];
                        }
                    }
                }

            }

            $keyData['answer'] = json_encode([$answer]);
            $keyData['mission_en'] = json_encode($task);
            $keyData['wrong'] = json_encode($request['wrong-answer']);
            $keyData['link_words'] = json_encode($request['additional_task']);
            $keyData['game_description_id'] = $request['description'];
            $keyData['audio'] = $this->audio;
            $keyData['read_type'] = $request['read_type'] ?? 0;
            $keyData['sign'] = $request['sign'] ?? 0;
            $keyData['capital'] = $request['capital'] ?? 0;
            $keyData['hint_option'] = $request['hint_option'];
            $game = Game::create($keyData);
            if ($request['hint_option'] == 1) {
                GameWord::create([
                    'game_id' => $game->id,
                    'word_id' => $request['hint'],
                    'type' => 4,
                ]);
            }
        } else {
            if (isset($request['audio'])) {
                $this->audio = 1;
            }
            $game = Game::create([
                'input_type' => 1,
                'game_description_id' => $request['description'],
                'audio' => $this->audio,
                'read_type' => $request['read_type'] ?? 0,
                'hint_option' => $request['hint_option'],
            ]);
            try {
                $task = $request['task-audio'][0] ?? $request['task-text'][0];

            } catch (Exception $e) {
                return response('image type games cannot be create in manual "automatic" way', 418);
            }

            GameWord::create([
                'game_id' => $game->id,
                'word_id' => $task,
                'type' => 1,
            ]);
            GameWord::create([
                'game_id' => $game->id,
                'word_id' => $request['correct_answer'][0],
                'type' => 2,
            ]);
            if ($request['hint_option'] == 1) {
                GameWord::create([
                    'game_id' => $game->id,
                    'word_id' => $request['hint'],
                    'type' => 4,
                ]);
            } else {
                $keyData = [];
                [$keys, $values] = \Illuminate\Support\Arr::divide($request->all());
                foreach ($keys as $index => $key) {
                    if (Str::contains($key, 'hint_')) {
                        if ($key <> 'hint_option') {
                            $keyData [$keys[$index]] = $values[$index];
                        }
                    }
                }
                $game->update($keyData);
            }
            foreach ($request['wrong-answer'] as $item)
                GameWord::create([
                    'game_id' => $game->id,
                    'word_id' => $item,
                    'type' => 3,
                ]);
        }
        return $game;
    }

    private function eruditeStore($request, $gameAnswerType): Game
    {
        if ($request['input_type'] == null || $request['input_type'] == 'manual') {
            $keyData = $this->gamesWithTagsProcessingFunction($request);
            if ($request['hint_option'] <> 1) {

                [$keys, $values] = \Illuminate\Support\Arr::divide($request->all());
                foreach ($keys as $index => $key) {
                    if (Str::contains($key, 'hint_')) {
                        if ($key <> 'hint_option') {
                            $keyData [$keys[$index]] = $values[$index];
                        }
                    }
                }

            }
            $keyData['hint_option'] = $request['hint_option'];

            $keyData['input_type'] = 0;
            $game = Game::create($keyData);
            if ($request['hint_option'] == 1) {
                GameWord::create([
                    'game_id' => $game->id,
                    'word_id' => $request['hint'],
                    'type' => 4,
                ]);
            }
        } else {
            $keyData = $this->gamesWithTagsProcessingFunction($request);
            $keyData['input_type'] = 1;
            $game = Game::create($keyData);
            if ($gameAnswerType->task == 'text') {
                GameWord::create([
                    'game_id' => $game->id,
                    'word_id' => $request['mission_en'],
                    'type' => 1
                ]);
            } elseif ($gameAnswerType->task == 'audio')
                GameWord::create([
                    'game_id' => $game->id,
                    'word_id' => $request['mission_en'],
                    'type' => 0
                ]);
            if ($request['hint_option'] == 1) {
                GameWord::create([
                    'game_id' => $game->id,
                    'word_id' => $request['hint'],
                    'type' => 4,
                ]);
            } else {
                $keyData = [];
                [$keys, $values] = \Illuminate\Support\Arr::divide($request->all());
                foreach ($keys as $index => $key) {
                    if (Str::contains($key, 'hint_')) {
                        if ($key <> 'hint_option') {
                            $keyData [$keys[$index]] = $values[$index];
                        }
                    }
                }
                $game->update($keyData);
            }
        }
        return $game;
    }

    private function linkWordsStore($request, $gameAnswerType)
    {
        $request['correct_answer'] = array_merge($request['correct_answer'], $request['correct-answer_link']);
        if ($request['input_type'] == null || $request['input_type'] == 'manual') {
            $readType = $request['read_type'] ?? 0;
            if (isset($request['audio'])) {
                $this->audio = 1;
            }
            $game = Game::create([
                'answer' => json_encode($request['correct_answer']),
                'mission_en' => json_encode($request['task-text'] ?? $request['task-audio']),
                'wrong' => json_encode($request['wrong-answer']),
                'link_words' => json_encode($request['additional_task']),
                'game_description_id' => $request['description'],
                'read_type' => $readType,
                'audio' => $this->audio
            ]);
        } else {
            $readType = $request['read_type'] ?? 0;
            $game = Game::create([
                'game_description_id' => $request['description'],
                'input_type' => 1,
                'read_type' => $readType,
            ]);
            $localised = 0;
            if ($request['localised'] == 1)
                $localised = 1;
            if ($gameAnswerType->additional_task == 'audio') {
                foreach ($request['correct_answer'] as $key => $item) {
                    if ($key < count($request['correct_answer']) / 2) {
                        if (isset($item)) {
                            $word = GameWord::create([
                                'game_id' => $game->id,
                                'word_id' => $item,
                                'type' => 6
                            ]);
                            if (isset($request['correct_answer'][count($request['correct_answer']) / 2 + $key]))

                                GameWord::create([
                                    'game_id' => $game->id,
                                    'word_id' => $request['correct_answer'][count($request['correct_answer']) / 2 + $key],
                                    'type' => 6,
                                    'link_words' => $word->id,
                                    'localised' => $localised,
                                ]);
                        }
                    }
                }
            } elseif ($gameAnswerType->additional_task == 'text') {
                foreach ($request['correct_answer'] as $key => $item) {
                    if ($key < count($request['correct_answer']) / 2) {
                        if (isset($item)) {
                            $word = GameWord::create([
                                'game_id' => $game->id,
                                'word_id' => $item,
                                'type' => 4
                            ]);
                            if (isset($request['correct_answer'][count($request['correct_answer']) / 2 + $key]))

                                GameWord::create([
                                    'game_id' => $game->id,
                                    'word_id' => $request['correct_answer'][count($request['correct_answer']) / 2 + $key],
                                    'type' => 4,
                                    'link_words' => $word->id,
                                    'localised' => $localised,
                                ]);
                        }
                    }
                }
            }
        }

        return $game;
    }

    private function gamesWithTagsProcessingFunction($request)
    {
        $keyData = [];
        $answers = json_decode($request['correct_answer']);
        $clues = json_decode($request['clue']);
        $wrongAnswers = json_decode($request['incorrect_answer']);

        $keyData['game_description_id'] = $request['description'];
        [$keys, $values] = Arr::divide($request->all());
        for ($i = 4; $i < count($keys); $i++) {
            $keyData[$keys[$i]] = $values[$i];
        }
        $keyData['answer'] = json_encode($answers);
        $keyData['link_words'] = json_encode($clues);
        unset($keyData['audio']);
        if (isset($wrongAnswers) && !empty($wrongAnswers)) {
            $keyData['wrong'] = json_encode($wrongAnswers);
        } else {
            $keyData['wrong'] = null;
        }
        if (isset($request['task-text'])) {
            $keyData['mission_en'] = json_encode($request['task-text']);
        }
        if ($request['input_type'] == null || $request['input_type'] == 'manual') {
            $keyData['input_type'] = 0;

        } else {
            $keyData['input_type'] = 1;

        }
        return $keyData;
    }

    private function spaceDataBuild($request)
    {
        $answers = json_decode($request['answers']);
        $clues = json_decode($request['clues']);
        $tasks = json_decode($request['task']);

        if (isset($request['clue'])) {
            unset($request['clue']);
        }
        if (isset($request['incorrect_answer'])) {
            $wrongAnswers = json_decode($request['incorrect_answer']);
        }

        if (!isset($request['wrong-answer'])) {
            if (isset($wrongAnswers) && !empty($wrongAnswers)) {
                $request['wrong-answer'] = $wrongAnswers;
            } else {
                $request['wrong-answer'] = null;
            }
        }

        $gameFunctions = new Games();

        $clues = $gameFunctions->cluesArrayBuild($tasks, $clues);
        $answers = $gameFunctions->doubleAnswerCheck($answers);
        $tasks = $gameFunctions->deleteAdditionalSimbolsFromTask($tasks);

        $request['correct_answer'] = $answers;
        $request['task-text'] = $tasks;
        $request['additional_task'] = $clues;

        return $request;
    }
}
