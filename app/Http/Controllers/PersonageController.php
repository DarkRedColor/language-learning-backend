<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Services\HostChecker;
use App\Http\Controllers\Services\Version;
use App\Models\Emotion;
use App\Models\Intro;
use App\Models\Language;
use App\Models\Personage;
use Http;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class PersonageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $personages = Personage::orderBy('name_en')->get();

        return view('admin.personages.index')->with(compact('personages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return string
     */
    public function create()
    {
        $languages = (new Services\Language())->getActiveLanguage();

        return view('admin.personages.includes.create')->with(compact('languages'))->render();
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

        $personageColl = Personage::create($keyData);
        $versionFunctions = new Version();
        $versionFunctions->versionInit($versionFunctions->typeGet('personages'),$personageColl->id);

        return redirect(route('personage-index'))->with('success', 'personage is created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return string
     */
    public function edit($id)
    {
        $personage = Personage::find($id);
        $languages = Language::where('deleted', 1)->get();

        return view('admin.personages.includes.edit')->with(compact('personage', 'languages'))->render();
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
            'name_en' => 'required'
        ]);

        [$keys, $values] = Arr::divide($request->all());
        $keyData = [];
        for ($i = 1; $i < count($keys); $i++) {
            $keyData [$keys[$i]] = $values[$i];
        }

        Personage::find($id)->update($keyData);
        $versionFunctions = new Version();
        $versionFunctions->versionIncrease($versionFunctions->typeGet('personages'),$id);

        return redirect(route('personage-index'))->with('success', 'personage is updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public
    function destroy($id)
    {
        if (\Auth::check() && \Auth::user()->hasRole(['admin','qa','project','developer'])) {
            $personage = Personage::find($id);
            $arrOfModels = [new Intro()];
            foreach ($arrOfModels as $model) {
                foreach ($model->where('personage_id',$id)->get() as $item) {

                    $item->update([
                        'personage_id' => null
                    ]);
                }
            }
            $arr = [];
            foreach ($personage->emotions()->get() as $item) {
                $arr[] = $item->name;
            }

            $hostCheck = new HostChecker();
            $host = $hostCheck->hostCheck(config('values.host'));
            \Illuminate\Support\Facades\Http::post(config("values.api_url") . 'personage/delete/' . $host, ['arr' => $arr]);

            $personage->emotions()->delete();
            $personage->delete();

            return response('success', 200);
        }
        return response('error', 401);
    }
}
