<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Services\ApiRequest;
use App\Http\Controllers\Services\Games;
use App\Http\Controllers\Services\GameStore;
use App\Http\Controllers\Services\HostChecker;
use App\Http\Controllers\Services\Images;
use App\Http\Controllers\Services\Version;
use App\Models\Game;
use App\Models\GameDescription;
use App\Models\GameDescriptionPivot;
use App\Models\GameTemplate;
use App\Models\GameType;
use App\Models\GameWord;
use App\Models\Language;
use App\Models\Learning;
use App\Models\Practice;
use App\Models\SubChapter;
use App\Models\Theory;
use Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Str;
use function React\Promise\all;

class GameController extends Controller
{
    public function declarations($id): array
    {
        $game = Game::where('id', $id)->get()->toArray();
        $game = $game[0];

        $descriptions = GameDescription::get(['id', 'description_en'])->toArray();
        $data = [];
        foreach ($descriptions as $item) {
            $data[$item['id']] = $item['description_en'];
        }

        $theory = (new Services\Games())->theoriesGet();

        $languages = Language::get(['lcid']);

        return [$game, $theory, $languages, $data];
    }

    public function dataTheoryLanguages(): array
    {
        $descriptions = GameDescription::get(['id', 'description_en'])->toArray();
        $data = [];
        foreach ($descriptions as $item) {
            $data[$item['id']] = $item['description_en'];
        }

        $theory = (new Services\Games())->theoriesGet();

        $languages = Language::get(['lcid']);

        return [$data, $theory, $languages];
    }

    /**
     * Show the form for editing a new resource.
     *
     * @return string
     */
    public function edit($id, $practiceId, $templateId, $learningId)
    {
        $template = GameTemplate::find($templateId);
        $game = Game::with('gamesTemplate', 'theories')->find($id);
        if ($template->task == 'space' || $template->answer == 'words') {
            $gamesFunctions = new Games();
            $game->mission_en = $gamesFunctions->taskBuildForEdit($game->mission_en, $game->answer, $game->link_words);
        }
        $languages = (new Services\Language())->getActiveLanguage();

        $descriptions = GameDescription::all();

        $description = GameDescriptionPivot::where('practice_id', $practiceId)
            ->where('learning_id', $learningId)
            ->where('game_template_id', $templateId)->first();

        $allLearningTheory = Learning::with('theories')->find($learningId);
        $theories = $allLearningTheory->theories;

        $allWordsOfTheory = null;
        if (isset($allLearningTheory->theories)) {
            foreach ($allLearningTheory->theories as $item) {
                if ($item->type == 0) {
                    $allWordsOfTheory[] = Theory::with("words")->find($item->id)->words;
                }
            }
        }
        $selectedWord = GameWord::where('game_id', $game->id)->orderBy('created_at')->get();
        $localisedRight = 0;
        foreach ($selectedWord as $item) {
            if ($item->localised == 1) {
                $localisedRight = 1;
                break;
            }
        }
        if (count($selectedWord) == 4) {
            $selectedWord->prepend(null);
        }
        return view('admin.games.includes.edit')->with(compact(
            'id',
            'practiceId',
            'learningId',
            'template',
            'game',
            'templateId',
            'languages',
            'descriptions',
            'description',
            'allWordsOfTheory',
            'selectedWord',
            'localisedRight',
            'theories'
        ))->render();
    }

    public function setSubChapterOrder(Request $request, $sub_chapter_id)
    {
        $ids = $request['ids'];
        [$keys, $values] = Arr::divide($ids);
        $games = Game::where('sub_chapter_id', $sub_chapter_id)->get();
        foreach ($games as $game) {
            Game::where('id', $game->id)
                ->update([
                    'order' => null,
                    'sub_chapter_id' => null,
                ]);
        }

        for ($i = 0; $i < count($keys); $i++) {
            Game::where('id', $values[$i])
                ->update([
                    'order' => $keys[$i],
                    'sub_chapter_id' => $sub_chapter_id,
                ]);
        }

        return response('success', 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index($learningId, $practiceId, $id)
    {
        $learning = Learning::with('games', 'practices')->find($learningId);
        $practice = Practice::with('games')->find($practiceId);

        $gameTemplate = GameTemplate::with('games')->find($id);

        $practiceGames = $practice->games->pluck('id');

        $games = $gameTemplate->games->pluck('id');
        $learnGames = $learning->games;


        $learnGames = $learnGames->whereIn('id', $games)->sortBy('created_at');
        $learnGames = $learnGames->whereIn('id', $practiceGames)->sortBy('created_at');

        return view('admin.games.index')->with(compact('practice', 'learning', 'practiceId', 'gameTemplate', 'learnGames'));
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

    private $audio = 0;

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(Request $request, $practiceId, $templateId, $learningId)
    {
        $theory = json_decode($request->theory);

        $gameStoreFunctions = new GameStore();
        $game = $gameStoreFunctions->GameDataStore($request, $templateId);

        GameTemplate::find($templateId)->games()->attach($game->id);
        Learning::find($learningId)->games()->attach($game->id);
        Practice::find($practiceId)->games()->attach($game->id);
        Game::find($game->id)->theories()->attach($theory);
        $scene = SubChapter::where('learning_id', $learningId)->first();
        if (isset($scene)) {
            $versionFunctions = new Version();
            $versionFunctions->versionIncrease($versionFunctions->typeGet('scene'), $scene->id);
        }

        return redirect(route('game-index', [$learningId, $practiceId, $templateId]));
    }

    public
    function create($practiceId, $templateId, $learningId)
    {
        $template = GameTemplate::find($templateId);
        $game = null;
        $languages = (new Services\Language())->getActiveLanguage();
        $descriptions = GameDescription::all();
        $learning = Learning::with('practices', 'theories')->find($learningId);

        $theories = $learning->theories;


        $description = GameDescriptionPivot::where('practice_id', $practiceId)
            ->where('learning_id', $learningId)
            ->where('game_template_id', $templateId)->first();
        $allLearningTheory = Learning::with('theories')->find($learningId);
        $allWordsOfTheory = null;
        if (isset($allLearningTheory->theories)) {
            foreach ($allLearningTheory->theories as $item) {
                if ($item->type == 0) {
                    $allWordsOfTheory[] = Theory::with("words")->find($item->id)->words;
                }
            }
        }

        $selectedWord = null;

        return view('admin.games.includes.create')->with(compact('game',
            'template',
            'practiceId',
            'learningId',
            'templateId',
            'languages',
            'description',
            'descriptions',
            'allWordsOfTheory',
            'selectedWord',
            'theories',
        ))->render();
    }

    public
    function getUnicEditForm(Request $request, $id)
    {
        $types = GameType::all();
        $thisType = GameType::find($request['type_id']);

        foreach ($types as $type) {
            if (!$type->id === $request['type_id']) {
                return response('fail', 404);
            } else {
                return view('admin.includes.games.' . Str::kebab($thisType->name) . '-edit')->with(['variables' => $this->declarations($id)])->render();
            }
        }

        return response('fail', 404);
    }

    public
    function getUnicForm(Request $request)
    {
        $types = GameType::all();
        $thisType = GameType::find($request['type_id']);

        foreach ($types as $type) {
            if (!$type->id === $request['type_id']) {
                return response('fail', 404);
            } else {
                return view('admin.includes.games.' . Str::kebab($thisType->name))->with(['variables' => $this->dataTheoryLanguages()])->render();
            }
        }

        return response('fail', 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public
    function update(Request $request, int $id, $practiceId, $templateId, $learningId)
    {
        $inputTypeArr = [
            'selective' => 1,
            'manual' => 0
        ];
        $theories = json_decode($request->theory);
        $game = Game::find($id);
        $gameAnswerType = GameTemplate::find($templateId);

        $modelTheories = $game->theories();
        $modelTheories->detach();
        $modelTheories->attach($theories);

        if (isset($request['hint_option'])) {
            if ($request['hint_option'] <> 0 || $request['hint_option'] <> 1)
                $request['hint_option'] = $inputTypeArr[$request['hint_option']];
        }
        if (isset($request['correct-answer_link'])) {
            $request['correct_answer'] = array_merge($request['correct_answer'], $request['correct-answer_link']);

        } elseif (isset($request['answers'])) {
            $request = $this->spaceDataBuild($request);
        }
        if (isset($request['clue']) && !empty(json_decode($request['clue']))) {
            $this->eruditeUpdate($request, $game);

        } elseif ($gameAnswerType->answer == 'erudite') {
            $this->eruditeUpdate($request, $game);
        } elseif ($gameAnswerType->answer == 'sentence') {
            $keyData = $this->gamesWithTagsProcessingFunction($request);
            $game->update($keyData);
        } else {
            if (!isset($game->answer) && $gameAnswerType->answer !== 'type' && $request['input_type'] != 'selective') {
                if ($gameAnswerType->task == 'symbols') {
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
                    $game->update([
                        'link_words' => json_encode($link_answers),
                        'mission_en' => json_encode($request['task-text'] ?? $request['task-audio']),
                        'game_description_id' => $request['description'],
                        'audio' => $this->audio,
                        'read_type' => $request['read_type'] ?? 0,

                    ]);

                } else {
                    if (isset($request['audio'])) {
                        $this->audio = 1;
                    }
                    $game->update([
                        'answer' => json_encode($request['correct_answer']),
                        'mission_en' => json_encode($request['task-text']),
                        'wrong' => json_encode($request['wrong-answer']),
                        'link_words' => json_encode($request['additional_task']),
                        'game_description_id' => $request['description'],
                        'read_type' => $request['read_type'] ?? 0,
                        'audio' => $this->audio
                    ]);
                }
            } else {
                if (is_array($request['link_words'])) {
                    if ($request['input_type'] == null || $request['input_type'] == 'manual') {
                        $keyData = null;
                        [$keys, $values] = Arr::divide($request->all());
                        for ($i = 5; $i < count($keys); $i++) {
                            $keyData[$keys[$i]] = $values[$i];
                        }
                        unset($keyData['link_words']);
                        unset($keyData['audio']);
                        $keyData['link_words'] = json_encode($request['link_words']);
                        $keyData['game_description_id'] = $request['description'];
                        $keyData['input_type'] = 0;
                        if (isset($request['task-audio']))
                            $keyData['mission_en'] = json_encode($request['task-audio']);
                        $keyData['description_en'] = $request['additional_task'][0] ?? null;
                        $game->update($keyData);
                    } else {
                        $keyData['game_description_id'] = $request['description'];
                        $keyData['read_type'] = $request['read_type'] ?? 0;
                        $keyData['input_type'] = $inputTypeArr[$request['input_type']];
                        $game->update($keyData);
                        if ($gameAnswerType->task == 'text')
                            GameWord::where('game_id', $game->id)->where('type', 1)->update([
                                'word_id' => $request['mission_en'],
                            ]);
                        elseif ($gameAnswerType->task == 'audio') {
                            GameWord::where('game_id', $game->id)->where('type', 0)->update([
                                'word_id' => $request['task-audio'][0],
                            ]);
                        }
                        GameWord::where('game_id', $game->id)->where('type', 2)->update([
                            'word_id' => $request['link_words'][0],
                        ]);
                    }
                } elseif (isset($request['task-text'])) {

                    if ($request['input_type'] == null || $request['input_type'] == 'manual') {
                        $answer = $request['correct_answer'];
                        if (is_array($request['correct_answer'])) {
                            $answer = json_encode($request['correct_answer']);
                        }
                        if (isset($request['audio'])) {
                            $this->audio = 1;
                        }
                        $game->update([
                            'answer' => $answer,
                            'mission_en' => json_encode($request['task-text']),
                            'wrong' => json_encode($request['wrong-answer']),
                            'link_words' => json_encode($request['additional_task']),
                            'game_description_id' => $request['description'],
                            'capital' => $request['capital'] ?? 0,
                            'sign' => $request['sign'] ?? 0,
                            'read_type' => $request['read_type'] ?? 0,
                            'audio' => $this->audio
                        ]);
                    } else {
                        if (isset($request['audio'])) {
                            $this->audio = 1;
                        }
                        $game->update([
                            'input_type' => 1,
                            'game_description_id' => $request['description'],
                            'audio' => $this->audio,
                            'read_type' => $request['read_type'] ?? 0,

                        ]);

                        GameWord::where('game_id', $game->id)->where('type', 1)->update([
                            'game_id' => $game->id,
                            'word_id' => $request['task-text'][0],
                        ]);
                        $this->createGameWordsLinks($game, $request);
                    }
                } elseif (isset($request['task-audio'])) {
                    if ($request['input_type'] == null || $request['input_type'] == 'manual') {
                        if (isset($request['audio'])) {
                            $this->audio = 1;
                        }
                        $game->update([
                            'answer' => json_encode($request['correct_answer']),
                            'mission_en' => json_encode($request['task-audio']),
                            'wrong' => json_encode($request['wrong-answer']),
                            'link_words' => json_encode($request['additional_task']),
                            'game_description_id' => $request['description'],
                            'read_type' => $request['read_type'] ?? 0,
                            'capital' => $request['capital'] ?? 0,
                            'sign' => $request['sign'] ?? 0,
                            'audio' => $this->audio
                        ]);
                    } else {
                        if (isset($request['audio'])) {
                            $this->audio = 1;
                        }
                        $game->update([
                            'input_type' => 1,
                            'game_description_id' => $request['description'],
                            'read_type' => $request['read_type'] ?? 0,
                            'audio' => $this->audio
                        ]);

                        GameWord::where('game_id', $game->id)->where('type', 0)->update([
                            'game_id' => $game->id,
                            'word_id' => $request['task-audio'][0],
                        ]);
                        $this->createGameWordsLinks($game, $request);
                    }
                } else {
                    if ($request['input_type'] == null || $request['input_type'] == 'manual') {
                        if (isset($request['audio'])) {
                            $this->audio = 1;
                        }
                        $game->update([
                            'answer' => json_encode($request['correct_answer']),
                            'wrong' => json_encode($request['wrong-answer']),
                            'link_words' => json_encode($request['additional_task']),
                            'game_description_id' => $request['description'],
                            'read_type' => $request['read_type'] ?? 0,
                            'audio' => $this->audio
                        ]);
                    } else {
                        $inputType = 0;
                        if ($request['input_type'] == 'selective') {
                            $inputType = 1;
                        }
                        $game->update([
                            'game_description_id' => $request['description'],
                            'input_type' => $inputType,
                            'read_type' => $request['read_type'] ?? 0,
                        ]);
                        if (isset($request['correct-answer_link'])) {
                            GameWord::where('game_id', $game->id)->delete();
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
                        } else {

                            GameWord::where('game_id', $game->id)->update([
                                'word_id' => $request['correct_answer'][0],
                            ]);
                            $this->createGameWordsLinks($game, $request);

                        }
                    }
                }
            }
        }

        if (isset($request['dd_file']) && !empty($request['dd_file'])) {
            $deleted = $request->is_deleted;
            $file = $request->dd_file;
            if ($file <> null && $deleted <> null || $file <> null && $deleted == null) {
                $apiRequestFunctions = new ApiRequest();
                $apiRequestFunctions->httpPostRequestToApiStorage($id, $request, 'game', 'image', false);
            } elseif ($deleted <> null && $file == null) {
                $hostCheck = new HostChecker();
                $host = $hostCheck->hostCheck(config('values.host'));
                $response = Http::post(config('values.api_url') . 'game/delete/' . $id . '/' . $host);
                if ($response->status() == 200) {
                    Game::find($id)->update([
                        'answer' => null,
                    ]);
                }
            }
        }
        if (isset($request['is_deleted']) && !empty($request['is_deleted'])) {
            $deleted = $request->is_deleted;
            $file = $request->dd_file;
            if ($deleted <> null && $file == null) {


                    Game::find($id)->update([
                        'mission_en' => null,
                    ]);

            }
        }

        if (isset($request['dd_file_additional']) && !empty($request['dd_file_additional'])) {
            $deleted = $request->is_deleted_additional;
            $file = $request->dd_file_additional;
            if ($file <> null && $deleted <> null || $file <> null && $deleted == null) {
                $apiRequestFunctions = new ApiRequest();
                $apiRequestFunctions->httpPostRequestToApiStorage($id, $request, 'game/additional', 'additional-image', false, true);
            } elseif ($deleted <> null && $file == null) {


                Game::find($id)->update([
                    'theory' => null,
                ]);

            }
        }
        if (isset($request['is_deleted_additional']) && !empty($request['is_deleted_additional'])) {
            $deleted = $request->is_deleted_additional;
            $file = $request->dd_file_additional;
            if ($deleted <> null && $file == null) {

                    Game::find($id)->update([
                        'theory' => null,
                    ]);
                }

        }
        $disk = 'game/audio';

        if ($request['update-audio'] == 'true') {
            $apiRequest = new ApiRequest();
            if ($request->hasFile('audio')) {
                $audio = $request->file('audio');
                $nameLetter = str_replace(' ', '_', $id . '.mp3');
                if (Storage::disk('gcs')->exists("$disk/$nameLetter")) {
                    Storage::disk('gcs')->delete("$disk/$nameLetter");
                }
                $apiRequest->postSftpContent($audio, $nameLetter, 'game/audio');
                Game::find($id)->update([
                    'audio' => 1,
                ]);
            } else {
                Game::find($id)->update([
                    'audio' => 0,
                ]);
                $nameLetter = str_replace(' ', '_', $id . '.mp3');
                if (Storage::disk('gcs')->exists( 'game/audio/' . $nameLetter)) {
                    Storage::disk('gcs')->delete( 'game/audio/' . $nameLetter);
                }
                if (Storage::disk('gcs')->exists("$disk/$nameLetter")) {
                    Storage::disk('gcs')->delete("$disk/$nameLetter");
                }
            }
        }
        $keyData = [];
        if ($request->hint_option == 'selective') {
            $game->update([
                'hint_option' => 1,
            ]);
        }
        [$keys, $values] = \Illuminate\Support\Arr::divide($request->all());
        foreach ($keys as $index => $key) {
            if (\Illuminate\Support\Str::contains($key, 'hint_')) {
                if ($key <> 'hint_option') {
                    $keyData [$keys[$index]] = $values[$index];
                }
            }
            if ($request->hint) {
                GameWord::where('game_id', $game->id)->where('type', 4)->update([
                    'word_id' => $request->hint,
                ]);
            }
        }

        $game->update($keyData);
        $scene = SubChapter::where('learning_id', $learningId)->first();
        if (isset($scene)) {
            $versionFunctions = new Version();
            $versionFunctions->versionIncrease($versionFunctions->typeGet('scene'), $scene->id);
        }

        return redirect(route('game-index', [$learningId, $practiceId, $templateId]));
    }

    public function getContent($name)
    {
        $apiRequest = new ApiRequest();
        return $apiRequest->getSftpContent($name, 'game/audio', 'game/audio');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public
    function destroy($id)
    {
        if (\Auth::check() && \Auth::user()->hasRole(['admin', 'qa', 'project', 'developer', 'methodologist'])) {
            Game::find($id)->delete();
            GameWord::where('game_id', $id)->delete();
            return response('success', 200);
        }
        return response('error', 401);
    }

    public function createGameWordsLinks($game, Request $request): void
    {
        GameWord::where('game_id', $game->id)->where('type', 2)->update([
            'game_id' => $game->id,
            'word_id' => $request['correct_answer'][0],
        ]);
        GameWord::where('game_id', $game->id)->where('type', 3)->delete();

        foreach ($request['wrong-answer'] as $item) {
            GameWord::create([
                'game_id' => $game->id,
                'word_id' => $item,
                'type' => 3,
            ]);
        }
    }

    private function eruditeUpdate(Request $request, $game): void
    {
        if ($request['input_type'] == null || $request['input_type'] == 'manual') {
            $keyData = $this->gamesWithTagsProcessingFunction($request);
            $keyData['input_type'] = 0;
            $game->update($keyData);
        } else {
            $keyData = $this->gamesWithTagsProcessingFunction($request);
            $keyData['input_type'] = 1;
            $game->update($keyData);
            if (isset($request['mission_en'])) {
                GameWord::where('game_id', $game->id)->update([
                    'word_id' => $request['mission_en'],
                ]);
            }
        }
    }

}
