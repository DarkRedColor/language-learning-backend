<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Services\ApiRequest;
use App\Http\Controllers\Services\HostChecker;
use App\Http\Controllers\Services\Images;
use App\Http\Controllers\Services\Text;
use App\Http\Controllers\Services\Version;
use App\Models\Emotion;
use App\Models\Intro;
use App\Models\Personage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class EmotionController extends Controller
{
    public function getContent($name)
    {
        $apiRequest = new ApiRequest();
        return $apiRequest->getSftpContent($name, 'emotion', 'emotions');
    }

    public function tempUpload(Request $request)
    {
        if ($request->hasFile('image')) {
            $file = $request->file('image')[0];
            $textService = new Text();
            $name = str_replace(' ', '_', $file->getClientOriginalName());
            $name = str_replace(['.png', '(', ')'], '', $name);
            $bgsName = Emotion::get('name');
            $name = $textService->unicalCheck($bgsName, $name, 1, 2, 'name');
            Storage::disk('emotions')->putFileAs('', $file, $name . ".png");

            return $name;

        }
        if (isset($request['load'])){
            $name = $request['load'];
            if (Storage::disk('emotions')->exists($name)){
                return Storage::disk('emotions')->download($name);
            }
        }
        return '';
    }

    public function index($id)
    {
        $personage = Personage::find($id);

        return view('admin.emotions.index')->with(compact('personage'));

    }

    public function tempUploadDelete()
    {
        $file = request()->getContent();
        if (config('values.debugBar')) {
            $re = '/.+?(?=<)/m';
            $str = request()->getContent();
            preg_match_all($re, $str, $matches, PREG_SET_ORDER);
            $file = $matches[0][0];
        }
        if (Storage::disk('emotions')->exists('/' . $file . '.png')) {
            Storage::disk('emotions')->delete('/' . $file . '.png');
        }
        return response('');

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        if (isset($request['image'])) {
            $imageFunctions = new Images();
            foreach ($request->image as $image) {
                if (config('values.debugBar')) {
                    $re = '/.+?(?=<)/m';
                    $str = $image;
                    preg_match_all($re, $str, $matches, PREG_SET_ORDER);
                    $name = $matches[0][0];
                    $name = str_replace('.png', '', $name);
                } else {
                    $name = str_replace('.png', '', $image);
                }
                $image = Storage::disk('emotions')->get($name.'.png');
                $imageFunctions->pngConvertAdventures($name, 'emotions', $image, false);
                Emotion::create([
                    'name' => $name,
                    'image' =>  'emotion/' . $name,
                ]);
                $emotion = Emotion::orderBy('created_at', "desc")->first();

                Personage::find($request['id'])->emotions()->attach([
                    'emotion_id' => $emotion->id
                ]);
                $versionFunctions = new Version();
                $versionFunctions->versionIncrease($versionFunctions->typeGet('personages'),$request['id']);
            }
        }
        return redirect(route('emotion-index', $request['id']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return Emotion::find($id);
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
        if (isset($request['image'])) {
            $name = Emotion::find($id)->name;
            $imageFunctions = new Images();
            $file = $imageFunctions->updateNameCleaner($request);
            $imageFunctions->pngConvertAdventures($name, 'emotions', $file, false,);
            $versionFunctions = new Version();
            $versionFunctions->versionIncrease($versionFunctions->typeGet('personages'),$request->id);
        }
        return redirect(route('emotion-index', $request->id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (\Auth::check() && \Auth::user()->hasRole(['admin','qa','project','developer'])) {
            $arrOfModels = [new Intro()];
            foreach ($arrOfModels as $model) {
                $model->update([
                    'emotion_id' => null
                ]);
            }

            $emotion = Emotion::find($id);
            $emotion->delete();

            return response('success', 200);
        } else {
            return response('error', 401);
        }
    }

}
