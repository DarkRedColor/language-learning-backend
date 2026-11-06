<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameDescription;
use App\Models\GameDescriptionPivot;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class GameDescriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $gameDescriptions = GameDescription::all();
        $gameDescriptionLinked = GameDescriptionPivot::all();

        return view('admin.game-descriptions.index')->with(compact('gameDescriptions','gameDescriptionLinked'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return bool|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        $languages = Language::where('deleted', 1)->get();

        return view('admin.game-descriptions.includes.create')->with(compact('languages'))->render();
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
            'description_en' => 'required'
        ]);
        [$keys, $values] = Arr::divide($request->all());
        $keyData = [];
        for ($i = 1; $i < count($keys); $i++) {
            $keyData [$keys[$i]] = $values[$i];
        }


        $key = $request->description_en;
        $key = Str::replace(' ', '_', $key);
        if (strlen($key) > 30) {
            $key = substr($key, 0, 30);
        }

        $item = GameDescription::get('key');

        $response = (new Services\Text())->unicalCheck($item, $key, 1, 2, 'key');
        if ($response == "") {
            $keyData['key'] = $key;
        } else {
            $keyData['key'] = $response;
        }
        $entity = GameDescription::create($keyData);
        $id = $entity->id;
        return redirect(route('game-description-index'))
            ->with(compact('id'));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return bool
     */
    public function show($id)
    {
        return true;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return string
     */
    public function edit($id)
    {
        $gameDescription = GameDescription::find($id);

        $languages = Language::where('deleted', 1)->get();

        return view('admin.game-descriptions.includes.edit')->with(compact('gameDescription', 'languages'))->render();
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
            'description_en' => 'required',
        ]);

        [$keys, $values] = Arr::divide($request->all());
        $keyData = [];
        for ($i = 1; $i < count($keys); $i++) {
            $keyData [$keys[$i]] = $values[$i];
        }

        GameDescription::where('id', $id)
            ->update($keyData);

        return redirect(route('game-description-index'))->with(compact('id'));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (\Auth::check() && \Auth::user()->hasRole(['admin','qa','project','developer','methodologist'])) {
            GameDescription::find($id)->delete();
            GameDescriptionPivot::where('game_description_id', $id)->delete();
            Game::where('game_description_id',$id)->update([
                'game_description_id' => null
            ]);
            return response('success', 200);
        }
        return response('error', 401);
    }
}
