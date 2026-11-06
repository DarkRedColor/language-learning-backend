<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Services\CrudText;
use App\Models\ArcadeTheme;
use App\Models\Language;
use App\Models\Text;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ArcadeThemesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $themes = ArcadeTheme::orderBy('name_en')->get();
        $languages = Language::where('deleted', 1)->get();
        return view('admin.arcade-storage.index')->with(compact('themes', 'languages'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return string
     */
    public function create()
    {
        $languages = Language::where('deleted', 1)->get();

        return view('admin.arcade-storage.includes.create')->with(compact('languages'))->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $model = new ArcadeTheme();
        [$keys, $values] = Arr::divide($request->all());
        $keyData = [];
        for ($i = 1; $i < count($keys); $i++) {
            $keyData [$keys[$i]] = $values[$i];
        }
        $model->create($keyData);

        return redirect(route('arcade-storage'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return string
     */
    public function edit($id)
    {
        $theme = ArcadeTheme::find($id);
        $languages = Language::where('deleted', 1)->get();
        return view('admin.arcade-storage.includes.edit')
            ->with(compact('theme', 'languages'))->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        $model = new ArcadeTheme();
        $crud = new CrudText();
        $id = $crud->update($request, $id, $model);

        return redirect(route('arcade-storage'))
            ->with(compact('id'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (\Auth::check() && \Auth::user()->hasRole(['admin','qa','project','developer'])) {
            ArcadeTheme::where('id', $id)->delete();
            return response('success', 200);
        }
        return response('error', 401);
    }
}
