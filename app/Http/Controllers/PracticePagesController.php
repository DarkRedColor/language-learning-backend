<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameTemplate;
use App\Models\Practice;
use Illuminate\Http\Request;

class PracticePagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index($practiceId)
    {
        $practicePages = Practice::with('gameTemplates')->find($practiceId);

        return view('admin.practice-pages.index')->with(compact('practicePages', 'practiceId'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return string
     */
    public function create($practiceId)
    {
        $ids = [];
        foreach (Practice::with('gameTemplates')->find($practiceId)->gameTemplates as $item) {
            $ids [] = $item->id;
        }
        $gameTemplates = GameTemplate::whereNotIn('id', $ids)->get();
        return view('admin.practice-pages.includes.create')->with(compact('practiceId', 'gameTemplates'))->render();
    }


    public function store(Request $request, $practiceId)
    {
        foreach (json_decode($request->array_of_game_templates) as $item) {
            Practice::find($practiceId)->gameTemplates()->attach($item);
        }

        return redirect(route('practice-pages-index', $practiceId));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $practiceId)
    {
        if (\Auth::check() && \Auth::user()->hasRole(['admin','qa','project','developer','methodologist'])) {
            Game::whereHas('gamesTemplate', function($query) use ($id){
                $query->where('id', $id);
            })->whereHas('practices', function($query) use ($practiceId){
                $query->where('id', $practiceId);
            })->delete();
            Practice::find($practiceId)->gameTemplates()->detach($id);

            return response('success', 200);
        }
        return response('error', 401);
    }
}
