<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Services\CrudText;
use App\Models\Chapter;
use App\Models\Introduction;
use App\Models\Language;
use App\Models\Welcome;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class IntroductionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $texts = Introduction::orderBy('name_en')->get();
        $languages = Language::where('deleted', 1)->get();

        return view('admin.introductions.index')->with(compact('texts', 'languages'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return string
     */
    public function create()
    {
        $languages = Language::where('deleted', 1)->get();

        return view('admin.introductions.create')->with(compact('languages'))->render();

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $model = new Introduction();
        $crud = new CrudText();
        $id = $crud->store($request, $model);

        return redirect(route('introduction-index'))
            ->with(compact('id'));
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return string
     */
    public function edit($id)
    {
        $text = Introduction::find($id);
        $languages = Language::where('deleted', 1)->get();

        return view('admin.introductions.edit')
            ->with(compact('text', 'languages'))->render();
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
        $model = new Introduction();
        $crud = new CrudText();
        $id = $crud->update($request, $id, $model);
        return redirect(route('introduction-index'))
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
            $model = Chapter::where('introduction_id',$id)->get();
            if (isset($model)){
                foreach ($model as $item){
                    $item->update(['introduction_id'=>null]);
                }
            }
            Introduction::find($id)->delete();

            return response('success', 200);
        }
        return response('error', 401);
    }
}
