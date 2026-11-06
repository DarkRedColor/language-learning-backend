<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Services\ApiRequest;
use App\Http\Controllers\Services\Version;
use App\Models\Intro;
use App\Models\Language;
use App\Models\Personage;
use App\Models\SubChapter;
use Arr;
use Illuminate\Http\Request;

class DialogueController extends Controller
{
    public function getContent($name)
    {
        $apiRequest = new ApiRequest();
        return $apiRequest->getSftpContent($name, 'dialogue/audio', 'dialogue/audio');
    }
    public function getEmotion(Request $request)
    {
        $emos = Personage::find($request['id'])->emotions()->get(['id', 'name'])->toArray();

        foreach ($emos as $item) {
            $emotions[$item['id']] = $item['name'];
        }
        $emotions['null'] = 'default';
        return response($emotions);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $dialogue = SubChapter::where('type', 3)->orderBy('name_en')->get();

        return view('admin.dialogues.index')->with(compact(
            'dialogue'
        ));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return bool
     */
    public function create()
    {
        $languages = (new Services\Language())->getActiveLanguage();

        return view('admin.dialogues.includes.create')->with(compact('languages'))->render();
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
            'name_en' => 'required'
        ]);

        [$keys, $values] = Arr::divide($request->all());
        $keyData = [];

        for ($i = 1; $i < count($keys); $i++) {
            $keyData [$keys[$i]] = $values[$i];
        }

        $item = SubChapter::create($keyData);

        $id = $item->id;

        return redirect(route('dialogue-index'))->with(compact('id'));


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return string
     */
    public function edit($id)
    {
        $dialogue = SubChapter::find($id);
        $languages = Language::where('deleted', 1)->get();


        return view('admin.dialogues.includes.edit')->with(compact(
            'dialogue',
            'languages',
        ))->render();
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
        [$keys, $values] = Arr::divide($request->all());
        $keyData = [];
        for ($i = 1; $i < count($keys); $i++) {
            $keyData [$keys[$i]] = $values[$i];
        }

        $model = SubChapter::find($id);

        $model->update($keyData);

        if (isset($model->sub_chapter_id)) {
            $versionFunctions = new Version();
            $versionFunctions->versionIncrease($versionFunctions->typeGet('scene'), $model->sub_chapter_id);
        }

        return redirect(route('dialogue-index'))->with(compact('id'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (\Auth::check() && \Auth::user()->hasRole(['admin', 'qa', 'project', 'developer'])) {
            $dialogue = SubChapter::find($id);
            $intros = Intro::where('sub_chapter_id', $id)->get();
            foreach ($intros as $item) {
                Intro::find($item->id)->delete();
            }
            $dialogue->delete();

            return response('success', 200);
        }
        return response('error', 401);
    }
}
