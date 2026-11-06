<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Services\ApiRequest;
use App\Http\Controllers\Services\HostChecker;
use App\Http\Controllers\Services\Images;
use App\Http\Controllers\Services\Text;
use App\Models\Animation;
use App\Models\AnimationEmotion;
use App\Models\Background;
use App\Models\Intro;
use Faker\Guesser\Name;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AnimationController extends Controller
{
    public function getContent($name)
    {
        $apiRequest = new ApiRequest();
        return $apiRequest->getSftpContent($name, 'animation', 'animations');
    }

    public function tempUpload(Request $request)
    {

        if ($request->hasFile('image')) {
            $file = $request->file('image')[0];
            $textService = new Text();
            $name = str_replace(' ', '_', $file->getClientOriginalName());
            $name = str_replace(['.riv', '(', ')'], '', $name);
            $bgsName = Animation::get('name');
            $name = $textService->unicalCheck($bgsName, $name, 1, 2, 'name');
            Storage::disk('animations')->putFileAs('', $file, $name . ".riv");

            return $name;
        }
        if (isset($request['load'])) {
            $name = $request['load'];
            if (Storage::disk('animations')->exists($name)) {
                return Storage::disk('animations')->download($name);
            }
        }
        return '';
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
        if (Storage::disk('animations')->exists('/' . $file . '.riv')) {
            Storage::disk('animations')->delete('/' . $file . '.riv');
        }
        return response('');

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $backgrounds = Animation::orderBy('created_at', 'desc')->get();
        foreach ($backgrounds as $item) {
            $backgrounds->find($item->id)['animation'] = AnimationEmotion::where('animation_id', $item->id)->get();
        }

        return view('admin.animation.index')->with(compact('backgrounds'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $res = redirect(route('animation-index'));
        if ($request->hasFile('file')) {
            $apiRequest = new ApiRequest();
            $texts = Animation::get('name');
            $name = (new Text())->unicalCheck($texts, str_replace('"', "", $request->name), 1, 2, 'name', true);
            $name = str_replace('.riv', "", $name) . ".riv";
            $animation = $request->file('file');
            $emotions = json_decode($request["animation-list"]);
            $apiRequest->postSftpContent($animation, $name, 'animation');
            $createdAnimation = Animation::create([
                'name' => $name
            ]);
            foreach ($emotions as $item) {
                AnimationEmotion::create([
                    'name' => $item,
                    'animation_id' => $createdAnimation->id
                ]);
            }
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
        if ($request->hasFile('file')) {
            $apiRequest = new ApiRequest();
            $animation = $request->file('file');
            $emotions = json_decode($request["animation-list"]);
            $model = new AnimationEmotion();
            $existingAnimation = Animation::find($id);
            $existingAnimationList = $model->where('animation_id', $id)->get();
            $existingAnimationListNames = $existingAnimationList->pluck('name')->toArray();
            $itemToDelete = array_diff($existingAnimationListNames, $emotions);
            $itemToCreate = array_diff($emotions, $existingAnimationListNames);

            if (Storage::disk('gcs')->exists('animation/' . $existingAnimation->name)) {
                Storage::disk('gcs')->delete('animation/' . $existingAnimation->name);
            }
            $apiRequest->postSftpContent($animation, $existingAnimation->name, 'animation');

            foreach ($itemToDelete as $item) {
                $dataBaseItem = $existingAnimationList->where('name', $item)->first();
                if (isset($dataBaseItem)) {
                    $dataBaseItem->delete();
                }
            }
            foreach ($itemToCreate as $item) {
                $model->create([
                    'name' => $item,
                    'animation_id' => $id
                ]);
            }
        }
        return redirect(route('animation-index'));

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
            $existingAnimation = Animation::find($id);

            if (Storage::disk('gcs')->exists( 'animation/' . $existingAnimation->name)) {
                Storage::disk('gcs')->delete( 'animation/' . $existingAnimation->name);
            }
            $existingAnimation->delete();

            return response('success', 200);
        } else {
            return response('error', 401);
        }
    }
}
