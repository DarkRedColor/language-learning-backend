<?php

namespace App\Http\Controllers;

use App\Models\GameType;
use Illuminate\Http\Request;

class GameTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        if (\Auth::check() && \Auth::user()->hasRole('admin')) {

            $gameTypes = GameType::paginate(15);

            return view('admin.game-types.index')->with(compact('gameTypes'));
        }
        return response('error', 401);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        if (\Auth::check() && \Auth::user()->hasRole('admin')) {
            return view('admin.game-types.create');
        }
        return response('error', 401);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        if (\Auth::check() && \Auth::user()->hasRole('admin')) {
            GameType::create($request->all());

            return redirect(route('game-type-index'))->with('success', 'Game type is created successfully');
        }
        return response('error', 401);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (\Auth::check() && \Auth::user()->hasRole('admin')) {

            $gameType = GameType::find($id);

            return view('admin.game-types.edit')->with(compact('gameType'));
        }
        return response('error', 401);
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
        if (\Auth::check() && \Auth::user()->hasRole('admin')) {

            GameType::find($id)->update($request->all());

            return redirect(route('game-type-index'))->with('success', 'Game type is updated successfully');
        }
        return response('error', 401);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (\Auth::check() && \Auth::user()->hasRole('admin')) {
            GameType::find($id)->delete();

            return response('success', 200);
        }
        return response('error', 401);
    }
}
