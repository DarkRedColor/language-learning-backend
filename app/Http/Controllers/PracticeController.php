<?php

namespace App\Http\Controllers;

use App\Models\GameDescriptionPivot;
use App\Models\Practice;
use Illuminate\Http\Request;

class PracticeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $practices = Practice::orderBy('created_at', 'desc')->get();

        return view('admin.practices.index')->with(compact('practices'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return string
     */
    public function create()
    {
        return view('admin.practices.includes.create')->render();
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
            'name' => 'required',
        ]);
        Practice::create([
            'name' => $request['name'],
        ]);

        return redirect(route('practice-index'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return string
     */
    public function edit($id)
    {
        $practice = Practice::find($id);

        return view('admin.practices.includes.edit')->with(compact('practice'))->render();
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
        Practice::find($id)->update([
            'name' => $request['name'],
        ]);

        return redirect(route('practice-index'));
    }

    public function deleteModal($id)
    {
        $practices = Practice::where('id', '!=', $id)->get();

        return view('admin.practices.includes.delete-modal')->with(compact('practices'))->render();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $val)
    {
        if (\Auth::check() && \Auth::user()->hasRole(['admin','qa','project','developer','methodologist'])) {

            $gameTemplate = Practice::with('gameTemplates')->find($id);
            GameDescriptionPivot::where('practice_id',$id)->delete();
            $ids = $gameTemplate->gameTemplates->pluck('id');
            $practice = Practice::with('gameTemplates')->find($val);
            $oldIds = $practice->gameTemplates->pluck('id');
            $ids = $ids->diff($oldIds);
            $practice->gameTemplates()->attach($ids);
            $gameTemplate->delete();

            return response('success', 200);
        }
        return response('error', 401);


    }
}
