<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameDescriptionPivot;
use App\Models\GameTemplate;
use Illuminate\Http\Request;

class GameTemplatesController extends Controller
{

    public function deleteModal($id)
    {
        $templates = GameTemplate::where('id', '!=', $id)->get();

        return view('admin.game-templates.includes.delete-modal')->with(compact('templates'))->render();

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $templates = GameTemplate::all();
        return view('admin.game-templates.index')->with(compact('templates'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return string
     */
    public function create()
    {
        $game = null;
        return view('admin.game-templates.includes.create')->with(compact('game'))->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $data = $request->all();
        if (array_key_exists('additional', $data)) {
            $data['additional_task'] = $data['additional'];
        }
        GameTemplate::create($data);

        return redirect(route('game-templates-index'));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return string
     */
    public function show($id)
    {
        $item = GameTemplate::find($id);
        $game = null;
        $languages = (new Services\Language())->getActiveLanguage();
        $allWordsOfTheory = null;
        $selectedWord = null;

        return view('admin.game-templates.includes.preview')->with(compact('item', 'game', 'languages', 'allWordsOfTheory', 'selectedWord'))->render();

    }

    public function destroy($id, $val)
    {
        if (\Auth::check() && \Auth::user()->hasRole(['admin','qa','project','developer','methodologist'])) {
            $gameTemplate = GameTemplate::with('games')->find($id);
            $ids = $gameTemplate->games->pluck('id');
            GameTemplate::find($val)->games()->attach($ids);
            GameDescriptionPivot::where('game_template_id', $id)->delete();
            $gameTemplate->delete();
            return response('success', 200);
        }
        return response('error', 401);

    }
}
