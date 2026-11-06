<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Services\Version;
use App\Models\ArcadeGame;
use App\Models\ArcadeTheme;
use App\Models\Background;
use Illuminate\Http\Request;

class ArcadeGamesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $arcades = ArcadeGame::all();

        return view('admin.arcade-games.index')->with(compact('arcades'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        $bg = Background::get(['id', 'name']);
        $themes = ArcadeTheme::get(['id', 'name_en']);


        return view('admin.arcade-games.includes.create')->with(compact(
            'bg',
            'themes'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $model = new ArcadeGame();
        $record = $model->create([
            'type' => $request->type,
            'start_background_id' => $request->arcade_start_id,
            'game_background_id' => $request->arcade_bg_id,
        ]);
        $versionFunctions = new Version();
        $versionFunctions->versionInit($versionFunctions->typeGet('arcade'),$record->id);
        foreach (json_decode($request->link_words) as $item) {
            $model->find($record->id)->arcadeThemes()->attach($item);
        }

        return redirect(route('arcade-games'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit($id)
    {
        $arcade = ArcadeGame::with('arcadeThemes')->find($id);
        $bg = Background::get(['id', 'name']);
        $themes = ArcadeTheme::get(['id', 'name_en']);



        return view('admin.arcade-games.includes.edit')->with(compact('arcade','bg','themes'));
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
        $model = ArcadeGame::find($id);
        $theories = null;
        if (isset($request->link_words)) {
            $theories = json_decode($request->link_words);
        }
        $modelArcadeTheme = $model->arcadeThemes();
        $modelArcadeTheme->detach();

        if (isset($theories)) {
            $modelArcadeTheme->attach($theories);
        }
        $model->update([
            'start_background_id' => $request->arcade_start_id,
            'game_background_id' => $request->arcade_bg_id,
        ]);
        $versionFunctions = new Version();
        $versionFunctions->versionIncrease($versionFunctions->typeGet('arcade'),$id);

        return redirect(route('arcade-games'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (\Auth::check() && \Auth::user()->hasRole(['admin','qa','project','developer'])) {

            ArcadeGame::find($id)->delete();

            return response('success', 200);
        }
        return response('error', 401);
    }
}
