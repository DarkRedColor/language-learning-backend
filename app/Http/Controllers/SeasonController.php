<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Services\Adventure;
use App\Http\Controllers\Services\ApiRequest;
use App\Http\Controllers\Services\HostChecker;
use App\Http\Controllers\Services\Images;
use App\Http\Controllers\Services\Version;
use App\Models\Chapter;
use App\Models\Episode;
use App\Models\Language;
use App\Models\Season;
use App\Models\Welcome;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SeasonController extends Controller
{
    public function getContent($name)
    {
        $apiRequest = new ApiRequest();
        return $apiRequest->getSftpContent($name, 'season', 'season');
    }

    private function apirequest($request, $id)
    {
        $apiRequest = new ApiRequest();
        $response = $apiRequest->imagesRequestDataFill($request->dd_file, $id, 'season');

        if ($response->status() == 200) {
            Season::where('id', $id)->update([
                'image_locked' => config('values.api_url') . 'season/' . $id . '_locked',
                'image_unlocked' => config('values.api_url') . 'season/' . $id,
            ]);
        }
    }

    private function imageDelete($id)
    {
        if (Storage::disk('gcs')->exists("season/$id")) {
            Storage::disk('gcs')->delete("season/$id");
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index($filter = "all")
    {
        $seasons = Season::whereNotNull('order')->orderBy('order')->get();

        return view('admin.seasons.index')->with(compact('seasons', 'filter'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return string
     */
    public function create()
    {
        $languages = Language::where('deleted', 1)->get();

        return view('admin.seasons.includes.create')->with(compact('languages'))->render();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function setSeasonOrder(Request $request)
    {
        $ids = $request['ids'];
        [$keys, $values] = Arr::divide($ids);
        $seasons = Season::all();
        foreach ($seasons as $season) {
            Season::where('id', $season->id)
                ->update([
                    'order' => null,
                ]);
        }
        for ($i = 0; $i < count($keys); $i++) {
            Season::where('id', $values[$i])
                ->update([
                    'order' => $keys[$i],
                ]);
        }

        return response('success', 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_en' => 'required',
        ]);
        $seasonsCount = count(Season::all());

        [$keys, $values] = Arr::divide($request->all());
        $keyData = [];
        for ($i = 3; $i < count($keys); $i++) {
            $keyData [$keys[$i]] = $values[$i];
        }
        $keyData['order'] = $seasonsCount + 1;

        Season::create($keyData);

        $id = Season::orderBy('created_at', 'desc')->first();
        $id = $id->id; //its will need for file upload
        $versionFunctions = new Version();
        $versionFunctions->versionInit($versionFunctions->typeGet('adventure'), $id);
        if ($request->hasFile('image')) {
            $imageFunctions = new Images();
            $file = $request->file('image');
            $imageFunctions->pngConvertAdventures($id, 'season', $file );

            Season::find($id)->update([
                "image_unlocked" => 1
            ]);
             // test version, not finished

        }

        return redirect(route('season-index'))->with('success', 'Season is created successfully');
    }

    public function edit($id)
    {
        $season = Season::find($id);
        $languages = Language::where('deleted', 1)->get();
        $hostCheck = new HostChecker();
        $host = $hostCheck->hostCheck(config('values.host'));

        return view('admin.seasons.includes.edit')->with(compact('season', 'languages', 'host'))->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name_en' => 'required',
        ]);

        if (!empty($request->tree)) {
            $adventuresFunctions = new Adventure();
            $model = new Season ();
            $adventuresFunctions->entityOrderSet($model, json_decode($request['tree']));
        }
        [$keys, $values] = Arr::divide($request->all());
        $keyData = [];
        for ($i = 4; $i < count($keys); $i++) {
            $keyData [$keys[$i]] = $values[$i];
        }
        $imageFunctions = new Images();

        $deleted = $request->is_deleted;
        if ($deleted == "1") {
            $imageFunctions->deleteFiles($id,'season');
            Season::where('id', $id)->update([
                'image_locked' => null,
                'image_unlocked' => null,
            ]);
        }
        if ($request->hasFile('image')) {

            $file = $request->file('image');
            $imageFunctions->pngConvertAdventures($id, 'season', $file,true );
            Season::where('id', $id)->update([
                'image_locked' => 1,
                'image_unlocked' => 1,
            ]);
        }
        unset($keyData['image']);

        Season::where('id', $id)
            ->update($keyData);
        $versionFunctions = new Version();
        $versionFunctions->versionIncrease($versionFunctions->typeGet('adventure'), $id);

        return redirect(route('season-index'))->with('success', 'Season was update successfully');

    }

    public function modalViewGet($id)
    {
        $season = Season::where('id', $id)->get();
        $season = $season->all();
        $nameLang = [];
        $displayNameLang = [];
        $languages = Language::get();
        foreach ($languages as $lang) {
            $nameLang [] = "name_" . $lang->lcid;
        }
        foreach ($languages as $lang) {
            $displayNameLang [] = $lang->name;
        }
        return view('admin.includes.season-modal')->with(compact('season', 'displayNameLang', 'nameLang'))->render();
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
            Season::where('id', $id)
                ->delete();
            $imageFunctions = new Images();
            $imageFunctions->deleteFiles($id,'season');

            $episodes = Episode::where('season_id', $id)->get();
            $episodeController = new EpisodeController();
            if (isset($episodes) && !empty($episodes)) {
                foreach ($episodes as $item) {
                    if (isset($item) && !empty($item)) {
                        $episodeController->destroy($item->id);
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
        $model = new Season();
        $adventures->lockedOrUnlockedEntity($model, $id, $isClosed);

        return "success";
    }
}
