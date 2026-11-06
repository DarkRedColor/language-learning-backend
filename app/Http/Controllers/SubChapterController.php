<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Services\Adventure;
use App\Http\Controllers\Services\ApiRequest;
use App\Http\Controllers\Services\HostChecker;
use App\Http\Controllers\Services\Images;
use App\Http\Controllers\Services\Version;
use App\Models\Chapter;
use App\Models\Episode;
use App\Models\File;
use App\Models\Game;
use App\Models\Language;
use App\Models\Learning;
use App\Models\Quiz;
use App\Models\Season;
use App\Models\SubChapter;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SubChapterController extends Controller
{
    public function getContent($name)
    {
        $apiRequest = new ApiRequest();
        return $apiRequest->getSftpContent($name, 'sub-chapter', 'sub-chapter');
    }
    public function theorySet(Request $request)
    {
        if ($request['theory_id'] === "null") {
            SubChapter::where('id', $request['id'])->update(['theory_id' => null]);
        }
        SubChapter::where('id', $request['id'])->update(['theory_id' => $request['theory_id']]);
        return response('success', 200);
    }
    private function imageDelete($id, $entity)
    {
        if (Storage::disk('gcs')->exists("$entity/$id")) {
            Storage::disk('gcs')->delete("$entity/$id");
        }
    }

//    private $grammarType = 0;
//    private $vocabularyType = 1;
//    private $wordType = 2;
//    private $storyType = 3;

    /**
     * Show the form for editing a new resource.
     *
     * @return string
     */
    public function modalViewGet($id)
    {
        $chapter = SubChapter::where('id', $id)->get();
        $nameLang = [];
        $descriptionLang = [];
        $displayNameLang = [];
        $displayDescriptionLang = [];
        $languages = Language::get();
        foreach ($languages as $lang) {
            $nameLang [] = "name_" . $lang->lcid;
            $descriptionLang[] = "description_" . $lang->lcid;
        }

        return view('admin.includes.sub-chapter-modal')->with(compact('chapter', 'displayNameLang', 'nameLang', 'displayDescriptionLang', 'descriptionLang'))->render();
    }

    /**
     *
     *
     * @return \Illuminate\Http\Response
     */

    public function setSubChapterOrder(Request $request, $chapter_id)
    {
        $ids = $request['ids'];
        [$keys, $values] = Arr::divide($ids);
        $chapters = SubChapter::where('chapter_id', $chapter_id)->get();
        foreach ($chapters as $chapter) {
            SubChapter::where('id', $chapter->id)
                ->update([
                    'order' => null,
                    'chapter_id' => null,
                ]);
        }

        for ($i = 0; $i < count($keys); $i++) {
            SubChapter::where('id', $values[$i])
                ->update([
                    'order' => $keys[$i],
                    'chapter_id' => $chapter_id,
                ]);
        }

        return response('success', 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index($season_id, $episode_id, $chapter_id, $filter = 'all')
    {

        $scenes = SubChapter::where('chapter_id', $chapter_id)
            ->whereNotNull('order')
            ->orderBy('order')
            ->get();
        $allScenes = SubChapter::all();


        $season = Season::find($season_id);
        $episode = Episode::find($episode_id);
        $chapter = Chapter::find($chapter_id);

        return view('admin.sub-chapter.index')->with(compact('scenes', 'season', 'chapter', 'episode', 'filter', 'allScenes'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return string
     */
    public function create($season_id, $episode_id, $chapter_id)
    {
        $languages = Language::where('deleted', 1)->get();
        $dialogues = SubChapter::where('type', 3)->where('sub_chapter_id', null)->get();
        $learnings = Learning::all();
        $type = Chapter::find($chapter_id)->type;

        switch ($type) {
            case (2):
                $res = view('admin.sub-chapter.includes.intro-scene-create')->with(compact('languages', 'chapter_id', 'episode_id', 'season_id', 'dialogues'))->render();
                break;
            case(1):
                $quizImg = Quiz::all();
                $res = view('admin.sub-chapter.includes.quiz-scene-create')->with(compact('learnings', 'languages', 'chapter_id', 'episode_id', 'season_id', 'dialogues', 'quizImg'))->render();
                break;
            case (3):
                $quizImg = Quiz::all();
                $res = view('admin.sub-chapter.includes.final-scene-create')->with(compact('learnings', 'languages', 'chapter_id', 'episode_id', 'season_id', 'dialogues', 'quizImg'))->render();
                break;
            default:
                $res = view('admin.sub-chapter.includes.chapter-scene-create')->with(compact('learnings', 'languages', 'chapter_id', 'episode_id', 'season_id', 'dialogues'))->render();
                break;
        }

        return $res;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(Request $request, $season_id, $episode_id, $chapter_id)
    {
        if ($request->quiz <> 1) {
            $request->validate([
                'name_en' => 'required'
            ]);
        } else {
            $keyData["name_en"] = 'quiz scene';
        }
        if ($request->quiz <> 1) {
            [$keys, $values] = Arr::divide($request->all());
            $keyData = [];
            if ($request->quiz == 3) {

                for ($i = 3; $i < count($keys); $i++) {
                    $keyData [$keys[$i]] = $values[$i];
                }

            } else {
                if (Chapter::find($chapter_id)->type == 2) {
                    for ($i = 3; $i < count($keys); $i++) {
                        $keyData [$keys[$i]] = $values[$i];
                    }
                } else {
                    for ($i = 4; $i < count($keys); $i++) {
                        $keyData [$keys[$i]] = $values[$i];
                    }
                }
            }
        }

        $keyData['order'] = count(SubChapter::where('chapter_id', $chapter_id)->get());
        $keyData['chapter_id'] = $chapter_id;

        $keyData['tags'] = null;

        if (!empty($request->tags)) {
            $keyData['tags'] = json_decode($request->tags);
        }
        if (!empty($request->array_of_learnings)) {
            $keyData['learnings'] = json_decode($request->array_of_learnings);
        }
        if (!empty($request->array_of_other)) {
            $keyData['other'] = json_decode($request->array_of_other);
        }
        if (!empty($request->image_id)) {
            $keyData['quiz_image'] = $request->image_id;
        }
        unset($keyData['image']);
        $sceneColl = SubChapter::create($keyData);
        $id = $sceneColl->id;

        if ($request->quiz == 3) {
            $hostFuntions = new HostChecker();
            $url = $hostFuntions->pythonHostCheck(config('values.host'));
            $data = ['scene_id' => $id,
                'timer' => $keyData['timer'],
                'game_count' => $keyData['count'],
                'action' => 0];
            $json = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);
            Http::withHeaders([
                'Authorization' => $hostFuntions->jwtTokenGen()
            ])
                ->withBody($json, 'application/json')
                ->post($url);
        }
        $versionFunctions = new Version();
        $versionFunctions->versionInit($versionFunctions->typeGet('scene'), $id);

        $dialogue_id = $request->dialogue_id ?? null;
        if (isset($dialogue_id)) {
            SubChapter::find($dialogue_id)->update([
                'sub_chapter_id' => $id
            ]);
        }
        $shortDialogueId = $request->short_dialogue_id ?? null;
        if (isset($shortDialogueId)) {
            SubChapter::find($shortDialogueId)->update([
                'sub_chapter_id' => $id,
                'short' => 1
            ]);
        }

        if ($request->hasFile('image')) {
            $imageFunctions = new Images();
            $file = $request->file('image');
            $imageFunctions->pngConvertAdventures($id, 'sub-chapter', $file);
            SubChapter::find($id)->update([
                'image_unlocked' => 1
            ]);

        }
        return redirect(route('sub-chapter-index', [$season_id, $episode_id, $chapter_id]))->with('success', 'Sub-chapter is created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return bool
     */
    public function edit($id, $season_id, $episode_id, $chapter_id)
    {
        $languages = Language::where('deleted', 1)->get();
        $scene = SubChapter::find($id);
        $chapter = Chapter::find($chapter_id);
        $episode = Episode::find($episode_id);
        $season = Season::find($season_id);
        $dialogues = SubChapter::where('type', 3)->get();
        $selectedDialogue = SubChapter::where('sub_chapter_id', $id)->where('short', 0)->first();
        $selectedShortDialogue = SubChapter::where('sub_chapter_id', $id)->where('short', 1)->first();
        $learnings = Learning::all();
        $selectedLearning = Learning::find($scene->learning_id);
        $hostCheck = new HostChecker();
        $host = $hostCheck->hostCheck(config('values.host'));

        if ($episode->type == 1) {
            $quizImg = Quiz::all();
            $res = view('admin.sub-chapter.includes.final-scene-edit')->with(compact('learnings', 'languages', 'chapter', 'scene', 'episode', 'season', 'dialogues', 'selectedDialogue', 'quizImg', 'selectedShortDialogue'))->render();
        } else {
            switch ($chapter->type) {
                case 2:
                    $res = view('admin.sub-chapter.includes.intro-scene-edit')->with(compact('languages', 'chapter', 'scene', 'episode', 'season', 'dialogues', 'selectedDialogue', 'host', 'selectedShortDialogue'))->render();
                    break;
                case 1:
                    $quizImg = Quiz::all();
                    $res = view('admin.sub-chapter.includes.quiz-scene-edit')->with(compact('learnings', 'languages', 'chapter', 'scene', 'episode', 'season', 'dialogues', 'selectedDialogue', 'quizImg', 'selectedShortDialogue'))->render();
                    break;
                default:
                    $res = view('admin.sub-chapter.includes.chapter-scene-edit')->with(compact('learnings', 'selectedLearning', 'languages', 'chapter', 'scene', 'episode', 'season', 'dialogues', 'selectedDialogue', 'host', 'selectedShortDialogue'))->render();
                    break;
            }
        }

        return $res;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */

    public function update(Request $request, int $id, $season_id, $episode_id, $chapter_id)
    {
        if ($request->quiz <> 1) {
            $request->validate([
                'name_en' => 'required',
            ]);
        } else {
            $keyData["name_en"] = 'quiz scene';
        }

        if (!empty($request->tree)) {
            $adventuresFunctions = new Adventure();
            $model = new SubChapter();
            $adventuresFunctions->entityOrderSet($model, json_decode($request['tree']));
        }
        if ($request->quiz <> 1) {

            [$keys, $values] = Arr::divide($request->all());
            $keyData = [];
            if ($request->quiz == 3) {
                for ($i = 3; $i < count($keys); $i++) {
                    $keyData [$keys[$i]] = $values[$i];
                }
            } else {
                if (Chapter::find($chapter_id)->type == 2) {
                    for ($i = 7; $i < count($keys); $i++) {
                        $keyData [$keys[$i]] = $values[$i];
                    }
                } else {
                    for ($i = 5; $i < count($keys); $i++) {
                        $keyData [$keys[$i]] = $values[$i];
                    }
                }
            }
        }
        $keyData['tags'] = null;

        if (!empty($request->tags)) {
            $keyData['tags'] = json_decode($request->tags);
        }
        if (!empty($request->array_of_learnings)) {
            $keyData['learnings'] = json_decode($request->array_of_learnings);
        }
        if (!empty($request->array_of_other)) {
            $keyData['other'] = json_decode($request->array_of_other);
        }
        if (!empty($request->image_id)) {
            $keyData['quiz_image'] = $request->image_id;
        } else {
            $keyData['quiz_image'] = null;
        }

        $subChapter = SubChapter::find($id);
        if ($request->quiz == 3) {
            $hostFuntions = new HostChecker();
            $url = $hostFuntions->pythonHostCheck(config('values.host'));
            $data = ['scene_id' => $id,
                'timer' => $keyData['timer'],
                'game_count' => $keyData['count'],
                'action' => 0];
            $json = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);
            Http::withHeaders([
                'Authorization' => $hostFuntions->jwtTokenGen()
            ])
                ->withBody($json, 'application/json')
                ->post($url);
        }
        $subChapter->update($keyData);

        $versionFunctions = new Version();
        $versionFunctions->versionIncrease($versionFunctions->typeGet('adventure'), $id);

        $dialogue_id = $request->dialogue_id ?? null;
        if (isset($dialogue_id)) {
            $dialogueSubChapter = SubChapter::where('id', $dialogue_id)->first();
            $check = SubChapter::where("sub_chapter_id", $id)->where('short', 0)->first();
            if (isset($check)) {
                if ($check->id <> $dialogueSubChapter->id) {
                    $check->update([
                        'sub_chapter_id' => null
                    ]);
                }
            }
            $dialogueSubChapter->update([
                'sub_chapter_id' => $id
            ]);

        } else {
            $check = SubChapter::where("sub_chapter_id", $id)->first();
            if (isset($check)) {
                $check->update([
                    'sub_chapter_id' => null
                ]);
            }
        }

        $shortDialogueId = $request->short_dialogue_id ?? null;
        if (isset($shortDialogueId)) {
            $dialogueSubChapter = SubChapter::where('id', $shortDialogueId)->first();
            $check = SubChapter::where("sub_chapter_id", $id)->where('short', 1)->first();
            if (isset($check)) {
                if ($check->id <> $dialogueSubChapter->id) {
                    $check->update([
                        'sub_chapter_id' => null,
                        'short' => 0
                    ]);
                }
            }
            $dialogueSubChapter->update([
                'sub_chapter_id' => $id,
                'short' => 1
            ]);

        } else {
            $check = SubChapter::where("sub_chapter_id", $id)->where('short', 1)->first();
            if (isset($check)) {
                $check->update([
                    'sub_chapter_id' => null,
                    'short' => 0,
                ]);
            }
        }
        $deleted = $request->is_deleted;
        $imageFunctions = new Images();

        if ($deleted == '1') {
            $imageFunctions->deleteFiles($id,'sub-chapter');
            SubChapter::where('id', $id)->update([
                'image_locked' => null,
                'image_unlocked' => null,
            ]);
        }
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageFunctions->pngConvertAdventures($id, 'sub-chapter',  $file);

        }

        return redirect(route('sub-chapter-index', [$season_id, $episode_id, $chapter_id]))->with('success', 'Sub chapter is created successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (\Auth::check() && !\Auth::user()->hasRole('content')) {
            $scene = SubChapter::find($id);

            $hostFuntions = new HostChecker();
            $url = $hostFuntions->pythonHostCheck(config('values.host'));
            $data = ['scene_id' => $id,
                'timer' => null,
                'game_count' => null,
                'action' => -1];
            $json = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);
            Http::withHeaders([
                'Authorization' => $hostFuntions->jwtTokenGen()
            ])
                ->withBody($json, 'application/json')
                ->post($url);

            SubChapter::where('sub_chapter_id', $id)->update([
                'sub_chapter_id' => null
            ]);
            $scene->delete();
            $imageFunctions = new Images();
            $imageFunctions->deleteFiles($id,'sub-chapter');


            return response('success', 200);
        }
        return response('error', 401);
    }


    public function isDisplayedToUser($id, $isClosed)
    {
        $adventures = new Adventure();
        $model = new SubChapter();
        $adventures->lockedOrUnlockedEntity($model, $id, $isClosed);

        return "success";
    }
}