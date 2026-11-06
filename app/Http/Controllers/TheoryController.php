<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Services\HostChecker;
use App\Http\Controllers\Services\Version;
use App\Models\Language;
use App\Models\SubChapter;
use App\Models\Theory;
use App\Models\TheoryPage;
use App\Models\TheoryPageName;
use App\Models\Word;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use function Symfony\Component\Translation\t;

class TheoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $theories = Theory::orderBy('created_at', 'desc')->get();

        return view('admin.theories.index')->with(compact('theories'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function wordIndex($id, $filter = 'word', $order = false)
    {

        $theories = Theory::with("words")->find($id);
        $languages = Language::where('deleted', 1)->get();

        if ($order == "true") {
            $order = true;
        } elseif ($order == "false") {
            $order = false;
        }

        return view('admin.words.theory-words.index')->with(compact('theories', 'filter', 'order', 'languages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return string
     */
    public function create($type = "word")
    {
        $res = view('admin.theories.includes.word-create')->render();
        switch ($type) {
            case ('grammar'):
                $languages = Language::where('deleted', 1)->get();
                $res = view('admin.theories.includes.grammar-create')->with(compact('type', 'languages'))->render();
                break;
            case ('topics'):
                $languages = Language::where('deleted', 1)->get();
                $res = view('admin.theories.includes.topic-create')->with(compact('type', 'languages'))->render();
                break;

        }
        return $res;
    }

    public function wordCreate($theoryId)
    {

        $ids = [];
        foreach (Theory::with('words')->find($theoryId)->words as $item) {
            $ids [] = $item->id;
        }
        $words = Word::whereNotIn('id', $ids)->get();

        return view('admin.words.theory-words.includes.create')->with(compact('words', 'theoryId'))->render();
    }

    public function wordStore(Request $request, $theoryId)
    {
        foreach (json_decode($request->array_of_words) as $item) {
            Theory::find($theoryId)->words()->attach($item);
        }
        return redirect(route('theory-words-index', $theoryId));

    }

    public function wordEdit($id, $theoryId)
    {
        $word = Word::find($id);
        $languages = Language::where('deleted', 1)->get();
        $hostCheck = new HostChecker();
        $host = $hostCheck->hostCheck(config('values.host'));

        return view('admin.words.includes.edit')->with(compact('word', 'languages', 'theoryId','host'))->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $stringType = $request->type;
        $type = 0;
        switch ($stringType) {
            case ('grammar'):
                $type = 1;
                break;
            case ('topics'):
                $type = 2;
                break;
        }
        if ($type == 0) {
            $tags = json_decode($request->tags);
            $name = $request->name;
            Theory::create([
                'name' => $name,
                'tags' => $tags,
                'type' => $type,
            ]);
        } else {
            $keyData = $this->getArr($request, $type);
            Theory::create($keyData);
        }


        return redirect(route('theory-index'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit($id)
    {
        $theory = Theory::find($id);
        $res = view('admin.theories.includes.word-edit')->with(compact('theory'));
        switch ($theory->type) {
            case (1):
                $languages = Language::where('deleted', 1)->get();
                $res = view('admin.theories.includes.grammar-edit')->with(compact('theory', 'languages'));
                break;
            case (2):
                $languages = Language::where('deleted', 1)->get();
                $res = view('admin.theories.includes.topic-edit')->with(compact('theory', 'languages'));
                break;
        }

        return $res;
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

        $stringType = $request->type;
        $type = 0;
        switch ($stringType) {
            case ('grammar'):
                $type = 1;
                break;
            case ('topics'):
                $type = 2;
                break;
        }
        if ($type == 0) {
            $tags = json_decode($request->tags);
            $name = $request->name;
            Theory::find($id)->update([
                'name' => $name,
                'tags' => $tags,
                'type' => $type,
            ]);
        } else {
            $keyData = $this->getArr($request, $type);
            Theory::find($id)->update($keyData);
        }
        $scene = SubChapter::where('theory_id',$id)->first();
        if (isset($scene)){
            $versionFunctions = new Version();
            $versionFunctions->versionIncrease($versionFunctions->typeGet('scene'),$scene->id);
        }
        return redirect(route('theory-index'));

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

            Theory::find($id)->delete();

            return response('success', 200);
        }
        return response('error', 401);
    }

    public function tGDestroy($id)
    {
        if (\Auth::check() && \Auth::user()->hasRole(['admin','qa','project','developer','methodologist'])) {
            $theoryPageName = TheoryPageName::where('theory_id', $id)->get();
            $ids = $theoryPageName->pluck('id');
            foreach ($ids as $item){
                $arrOfIds = TheoryPage::where('theory_page_name_id', $item)->pluck('id');
                TheoryPage::whereIn('id',$arrOfIds)->delete();
            }
            TheoryPageName::whereIn('id',$ids)->delete();
            Theory::find($id)->delete();
            return response('success', 200);
        }
        return response('error', 401);
    }

    public function wordDestroy($id, $theoryId)
    {
        if (\Auth::check() && \Auth::user()->hasRole(['admin','qa','project','developer','methodologist'])) {

            Theory::find($theoryId)->words()->detach($id);

            return response('success', 200);
        }
        return response('error', 401);
    }

    /**
     * @param Request $request
     * @param int $type
     * @return array
     */
    private function getArr(Request $request, int $type): array
    {
        [$keys, $values] = Arr::divide($request->all());
        $keyData = [];

        for ($i = 3; $i < count($keys); $i++) {
            $keyData [$keys[$i]] = $values[$i];
        }
        $keyData['tags'] = json_decode($keyData['tags']);
        $keyData['type'] = $type;
        return $keyData;
    }
}
