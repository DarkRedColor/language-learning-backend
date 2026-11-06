<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Services\Adventure;
use App\Http\Controllers\Services\ApiRequest;
use App\Http\Controllers\Services\HostChecker;
use App\Http\Controllers\Services\Images;
use App\Http\Controllers\Services\Version;
use App\Models\Background;
use App\Models\Chapter;
use App\Models\Emotion;
use App\Models\Episode;
use App\Models\Introduction;
use App\Models\Language;
use App\Models\Personage;
use App\Models\Season;
use App\Models\SubChapter;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use function React\Promise\all;

class ChapterController extends Controller
{
    public function getContent($name)
    {
        $apiRequest = new ApiRequest();
        return $apiRequest->getSftpContent($name, 'chapter', 'chapter');
    }
    /**
     * Show the form for editing a new resource.
     *
     * @return string
     */
    public function modalViewGet($id)
    {
        $chapter = Chapter::where('id', $id)->get();
        $bg = Background::get(['id', 'name'])->toArray();
        $bg = Arr::pluck($bg, 'name', 'id');
        $bg = ['null' => 'null'] + $bg;
        $nameLang = [];
        $descriptionLang = [];
        $displayNameLang = [];
        $displayDescriptionLang = [];
        $languages = Language::get();
        foreach ($languages as $lang) {
            $nameLang [] = "name_" . $lang->lcid;
            $descriptionLang[] = "description_" . $lang->lcid;
        }
        return view('admin.includes.chapter-modal')->with(compact('chapter', 'displayNameLang', 'bg', 'nameLang', 'displayDescriptionLang', 'descriptionLang'))->render();
    }

    /**
     *
     *
     * @return \Illuminate\Http\Response
     */

    private function imageDelete($id, $entity)
    {
        if (Storage::disk('gcs')->exists("$entity/$id")) {
            Storage::disk('gcs')->delete("$entity/$id");
        }
    }
    public function setChapterOrder(Request $request, $episode_id)
    {
        $ids = $request['ids'];
        [$keys, $values] = Arr::divide($ids);
        $chapters = Chapter::where('episode_id', $episode_id)->get();
        foreach ($chapters as $chapter) {
            Chapter::where('id', $chapter->id)
                ->update([
                    'order' => null,
                    'episode_id' => null,
                ]);
        }

        for ($i = 0; $i < count($keys); $i++) {
            Chapter::where('id', $values[$i])
                ->update([
                    'order' => $keys[$i],
                    'episode_id' => $episode_id,
                ]);
        }

        return response('success', 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index($season_id, $episode_id, $filter = "all")
    {

        $chapters = Chapter::where('episode_id', $episode_id)
            ->whereNotNull('order')
            ->orderBy('order')
            ->get();

        $season = Season::find($season_id);
        $episode = Episode::find($episode_id);

        return view('admin.chapters.index')->with(compact(
            'season_id',
            'episode_id',
            'chapters',
            'season',
            'episode',
            'filter'
        ));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return string
     */
    public function create($season_id, $episode_id, $type = 'chapter')
    {
        $languages = Language::where('deleted', 1)->get();

        $res = view('errors.404');

        switch ($type) {
            case('chapter'):
                $personages = Personage::with('emotions')->get(['id', 'name_en']);
                $introductions = Introduction::where('type', 2)->get();
                $res = view('admin.chapters.includes.chapter-create')->with(compact('languages', 'season_id', 'episode_id', 'personages', 'introductions'))->render();
                break;
            case ('quiz'):
                $res = view('admin.chapters.includes.quiz-create')->with(compact('languages', 'season_id', 'episode_id'))->render();
                break;
            case ('intro'):
                $res = view('admin.chapters.includes.intro-create')->with(compact('languages', 'season_id', 'episode_id'))->render();
        }

        return $res;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(Request $request, $season_id, $episode_id, $final = false)
    {

        if ($request->type <> 1) {
            $request->validate([
                'name_en' => 'required'
            ]);
        } else {
            $keyData["name_en"] = 'Quiz';
        }
        if ($request->type <> 1) {

            [$keys, $values] = Arr::divide($request->all());
            $keyData = [];
            for ($i = 5; $i < count($keys); $i++) {
                $keyData [$keys[$i]] = $values[$i];
            }
        }
        $keyData['order'] = count(Chapter::where('episode_id', $episode_id)->get());
        $keyData['episode_id'] = $episode_id;
        $keyData["type"] = $request->type;
        if (isset($request->emotion_id)) {
            $personage_id = Emotion::with('personage')->find($request->emotion_id)->personage[0]->id;
            $keyData['personage_id'] = $personage_id;
        }
        if ($final){
            $keyData['name_en'] = "Final Quiz";
        }
        unset($keyData['image']);
        $chapterColl = Chapter::create($keyData);
        $id = $chapterColl->id; //its will need for file upload

        $versionFunctions = new Version();
        $versionFunctions->versionInit($versionFunctions->typeGet('adventure'), $id);

        if ($request->hasFile('image')) {
            $imageFunctions = new Images();
            $file = $request->file('image');
            $imageFunctions->pngConvertAdventures($id, 'chapter',  $file);
            Chapter::find($id)->update([
                'image_unlocked' => 1
            ]);

        }
        if ($final) {
            return $id;
        } else {
            return redirect(route('chapter-index', [$season_id, $episode_id]))->with('success', 'Episode is created successfully');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return bool
     */
    public function edit($id, $season_id, $episode_id)
    {
        $languages = Language::where('deleted', 1)->get();
        $chapter = Chapter::find($id);
        $hostCheck = new HostChecker();
        $host = $hostCheck->hostCheck(config('values.host'));

        $res = view('errors.404');

        switch ($chapter->type) {
            case(0):
                $personages = Personage::with('emotions')->get(['id', 'name_en']);
                $introductions = Introduction::where('type', 2)->get();
                $res = view('admin.chapters.includes.chapter-edit')->with(compact('languages', 'chapter', 'season_id', 'episode_id', 'host', 'personages', 'introductions'))->render();
                break;
            case (1):
                $res = view('admin.coming-soon')->render();
                break;
            case (2):
                $res = view('admin.chapters.includes.intro-edit')->with(compact('languages', 'chapter', 'season_id', 'episode_id', 'host'))->render();
        }

        return $res;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id, $season_id, $episode_id)
    {
        $request->validate([
            'name_en' => 'required',
        ]);
        if (!empty($request->tree)) {
            $adventuresFunctions = new Adventure();
            $model = new Chapter();
            $adventuresFunctions->entityOrderSet($model, json_decode($request['tree']));
        }

        [$keys, $values] = Arr::divide($request->all());
        $keyData = [];
        for ($i = 5; $i < count($keys); $i++) {
            $keyData [$keys[$i]] = $values[$i];
        }
        $deleted = $request->is_deleted;
        if (isset($request->emotion_id)) {
            $personage_id = Emotion::with('personage')->find($request->emotion_id)->personage[0]->id;
            $keyData['personage_id'] = $personage_id;
        }
        Chapter::where('id', $id)
            ->update($keyData);

        $versionFunctions = new Version();
        $versionFunctions->versionIncrease($versionFunctions->typeGet('adventure'), $id);
        $imageFunctions = new Images();
        if ($deleted == '1') {
            $imageFunctions->deleteFiles($id,'chapter');
            Chapter::find($id)->update([
                'image_locked' => null,
                'image_unlocked' => null,
            ]);
        }
        if ($request->hasFile('image')) {

            $file = $request->file('image');
            $imageFunctions->pngConvertAdventures($id, 'chapter',  $file);
            Storage::disk('gcs')->putFileAs("/chapter/", $file, "$id"); // test version, not finished

        }

        return redirect(route('chapter-index', [$season_id, $episode_id]))->with('success', 'Chapter is created successfully');
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
            Chapter::find($id)->delete();
            $imageFunctions = new Images();
            $imageFunctions->deleteFiles($id,'chapter');


            $scenes = SubChapter::where('chapter_id', $id)->get();
            $subChapterController = new SubChapterController();
            if (isset($scenes) && !empty($scenes)) {
                foreach ($scenes as $item) {
                    if (isset($item) && !empty($item)) {
                        $subChapterController->destroy($item->id);
                    }
                }
            }


            return response('success', 200);
        }
        return response('error', 401);
    }

    private function httpPostRequestToApiStorage($id, $request)
    {
        $apiRequest = new ApiRequest();
        $response = $apiRequest->imagesRequestDataFill($request->dd_file, $id, 'chapter/image');
        $hostCheck = new HostChecker();
        $host = $hostCheck->hostCheck(config('values.host'));

        if ($response->status() == 200) {
            Chapter::where('id', $id)->update([
                'image_locked' => config('values.api_url') . 'chapter/image/' . $host . '/' . $id . '_locked',
                'image_unlocked' => config('values.api_url') . 'chapter/image/' . $host . '/' . $id,
            ]);
        }
    }

    public function isDisplayedToUser($id, $isClosed)
    {
        $adventures = new Adventure();
        $model = new Chapter();
        $adventures->lockedOrUnlockedEntity($model, $id, $isClosed);

        return "success";
    }
}
