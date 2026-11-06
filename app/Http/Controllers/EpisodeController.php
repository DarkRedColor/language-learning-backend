<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Services\Adventure;
use App\Http\Controllers\Services\ApiRequest;
use App\Http\Controllers\Services\HostChecker;
use App\Http\Controllers\Services\Images;
use App\Http\Controllers\Services\Version;
use App\Models\Background;
use App\Models\Chapter;
use App\Models\Episode;
use App\Models\Language;
use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use function React\Promise\all;

class EpisodeController extends Controller
{
    public function getContent($name)
    {
        $apiRequest = new ApiRequest();
        return $apiRequest->getSftpContent($name, 'episode', 'episode');
    }

    public function getContentAdditional($name)
    {
        $apiRequest = new ApiRequest();
        return $apiRequest->getSftpContent($name, 'episode/additional', 'episode/additional');
    }

    private function imageDelete($id, $entity)
    {
        if (Storage::disk('gcs')->exists("$entity/$id")) {
            Storage::disk('gcs')->delete("$entity/$id");
        }
    }


    /**
     * Show the form for editing a new resource.
     *
     * @return string
     */
    public function modalViewGet($id)
    {
        $episode = Episode::where('id', $id)->get();
        $nameLang = [];
        $descriptionLang = [];
        $displayNameLang = [];
        $displayDescriptionLang = [];
        $languages = Language::get();
        foreach ($languages as $lang) {
            $nameLang [] = "name_" . $lang->lcid;
            $descriptionLang[] = "description_" . $lang->lcid;
        }
        return view('admin.includes.episode-modal')->with(compact('episode', 'displayNameLang', 'nameLang', 'displayDescriptionLang', 'descriptionLang'))->render();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function setEpisodeOrder(Request $request, $season_id)
    {
        $ids = $request['ids'];
        [$keys, $values] = Arr::divide($ids);
        $episodes = Episode::where('season_id', $season_id)->get();
        foreach ($episodes as $episode) {
            Episode::where('id', $episode->id)
                ->update([
                    'order' => null,
                    'season_id' => null,
                ]);
        }
        for ($i = 0; $i < count($keys); $i++) {
            Episode::where('id', $values[$i])
                ->update([
                    'order' => $keys[$i],
                    'season_id' => $season_id,
                ]);
        }

        return response('success', 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return string
     */
    public function index($season_id, $filter = "all")
    {

        $episodes = Episode::with('chapters')->where('season_id', $season_id)
            ->whereNotNull('order')
            ->orderBy('order')
            ->get();

        $season = Season::find($season_id);

        return view('admin.episodes.index')->with(compact('episodes', 'season_id', 'season', 'filter'));

    }

    public function create($season_id)
    {
        $languages = Language::where('deleted', 1)->get();
        $bg = Background::get(['id', 'name']);

        return view('admin.episodes.includes.create')->with(compact('season_id', 'languages', 'bg'))->render();
    }

    public function finalQuizCreate($season_id)
    {
        $bg = Background::get(['id', 'name']);
        return view('admin.episodes.includes.final-quiz-create')->with(compact('season_id', 'bg'))->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(Request $request, $season_id)
    {
        $request->validate([
            'name_en' => 'required|max:30'
        ]);
        $episodes = Episode::where('season_id', $season_id)->get();
        [$keys, $values] = Arr::divide($request->all());
        $keyData = [];
        if ($request->type == 0) {
            if ($request->dd_file <> null) {
                for ($i = 6; $i < count($keys); $i++) {
                    $keyData [$keys[$i]] = $values[$i];
                }
            } else {
                for ($i = 3; $i < count($keys); $i++) {
                    $keyData [$keys[$i]] = $values[$i];
                }
            }
        } else {
            $keyData = [
                'type' => 1,
                'name_en' => $request->name_en,
                'background_id' => $request->background_id,
            ];
        }
        $keyData['season_id'] = $season_id;
        $keyData['order'] = count($episodes);
        unset($keyData['image']);
        unset($keyData['image-additional']);
        $episode = Episode::create($keyData);
        if ($request->type == 1) {
            $chapterFunctions = new ChapterController();
            $request->type = 3;
            $chapterFunctions->store($request, $season_id, $episode->id, true);
        }
        $versionFunctions = new Version();
        $versionFunctions->versionInit($versionFunctions->typeGet('adventure'), $episode->id);

        if ($request->hasFile('image')) {
            $imageFunctions = new Images();
            $file = $request->file('image');
            $imageFunctions->pngConvertAdventures($episode->id, 'episode',  $file);
            Episode::find($episode->id)->update([
                'image_unlocked'=>1
            ]);

        }

        if ($request->hasFile('image-additional')) {
            $imageFunctions = new Images();
            $file = $request->file('image-additional');
            $imageFunctions->pngConvertAdventures($episode->id, 'episode/additional',  $file);
            Episode::find($episode->id)->update([
                'additional_image_unlocked'=>1
            ]);
        }


        return redirect(route('episode-index', $season_id))->with('success', 'Episode is created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return bool|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit($id, $season_id)
    {
        $episode = Episode::find($id);
        $languages = Language::where('deleted', 1)->get();
        $bg = Background::get(['id', 'name']);

        return view('admin.episodes.includes.edit')->with(compact('languages', 'episode', 'season_id', 'bg'))->render();
    }

    public function finalQuizEdit($id, $season_id)
    {
        $episode = Episode::find($id);
        $bg = Background::get(['id', 'name']);
        $hostCheck = new HostChecker();
        $host = $hostCheck->hostCheck(config('values.host'));
        return view('admin.episodes.includes.final-quiz-edit')->with(compact('episode', 'season_id', 'host', 'bg'))->render();

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id, $season_id)
    {
        $request->validate([
            'name_en' => 'required',
        ]);

        if (!empty($request->tree)) {
            $adventuresFunctions = new Adventure();
            $model = new Episode();
            $adventuresFunctions->entityOrderSet($model, json_decode($request['tree']));
        }
        [$keys, $values] = Arr::divide($request->all());
        $keyData = [];
        if ($request->type == 0) {
            if ($request->dd_file <> null) {
                for ($i = 8; $i < count($keys); $i++) {
                    $keyData [$keys[$i]] = $values[$i];
                }
            } else {
                for ($i = 5; $i < count($keys); $i++) {
                    $keyData [$keys[$i]] = $values[$i];
                }
            }
        } else {
            $keyData = [
                'background_id' => $request->background_id
            ];
        }

        Episode::find($id)->update($keyData);

        $versionFunctions = new Version();
        $versionFunctions->versionIncrease($versionFunctions->typeGet('adventure'), $id);
        $imageFunctions = new Images();
        $deleted = $request->is_deleted;
        if ($deleted == '1') {
            $imageFunctions->deleteFiles($id,'episode');
            Episode::find($id)->update([
                'image_locked' => null,
                'image_unlocked' => null,
            ]);
        }
        $deleted = $request->is_deleted_additional;
        if ($deleted == '1') {
            $imageFunctions->deleteFiles($id,'episode/additional');
            Episode::find($id)->update([
                'additional_image_locked' => null,
                'additional_image_unlocked' => null,
            ]);
        }

        if ($request->hasFile('image')) {

            $file = $request->file('image');
            $imageFunctions->pngConvertAdventures($id, 'episode',  $file);

        }

        if ($request->hasFile('image-additional')) {
            $imageFunctions = new Images();
            $file = $request->file('image-additional');
            $imageFunctions->pngConvertAdventures($id, 'episode/additional',  $file);

        }

        return redirect(route('episode-index', $season_id))->with('success', 'Episode is created successfully');

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
            Episode::find($id)->delete();
            $imageFunctions = new Images();
            $imageFunctions->deleteFiles($id,'episode/additional');
            $imageFunctions->deleteFiles($id,'episode');


            $chapters = Chapter::where('episode_id', $id)->get();
            $chapterController = new ChapterController();

            if (isset($chapters) && !empty($chapters)) {
                foreach ($chapters as $item) {
                    if (isset($item) && !empty($item)) {
                        $chapterController->destroy($item->id);
                    }
                }
            }


            return response('success', 200);
        }
        return response('error', 401);
    }


    public function isDisplayedToUser($id, $isClosed)
    {
        $adventures = new Adventure();
        $model = new Episode();
        $adventures->lockedOrUnlockedEntity($model, $id, $isClosed);

        return "success";
    }
}
