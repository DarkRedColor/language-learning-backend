<?php

namespace App\Http\Controllers;

use App\Imports\ArcadeWordsImport;
use App\Models\ArcadeTheme;
use App\Models\ArcadeWord;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ArcadeWordsController extends Controller
{

    public function import(Request $request)
    {
        if ($request->hasFile("import-text")) {
            $file = $request->file("import-text");
            $arcadeThemeId = $request->arcadeThemeId;
            Excel::import(new ArcadeWordsImport($arcadeThemeId), $file);
            $res = redirect()->back()->with('success', 200);
        } else {
            $res = redirect()->back()->with('failed', 400);
        }
        return $res;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index($id)
    {
        $words = ArcadeWord::Where('arcade_theme_id',$id)->get();
        $arcade = ArcadeTheme::find($id);

        return view('admin.arcade-storage.theme_index')->with(compact('words','id','arcade'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create($id)
    {
        return view('admin.arcade-storage.includes.theme_word_create')->with(compact('id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(Request $request, $theme)
    {
        ArcadeWord::create([
            'word' => $request->word ?? 'undefined',
            'arcade_theme_id' => $theme
        ]);

        return redirect(route('arcade-storage-theme',$theme));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit($theme,$id)
    {
        $word = ArcadeWord::find($id);

        return view('admin.arcade-storage.includes.theme_word_edit')->with(compact('word','theme'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function update(Request $request,$theme, $id)
    {
        ArcadeWord::find($id)->update([
            'word' => $request->word
        ]);
        return redirect(route('arcade-storage-theme',$theme));
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
            ArcadeWord::find($id)->delete();
            return response('success', 200);
        }
        return response('error', 401);
    }
}
