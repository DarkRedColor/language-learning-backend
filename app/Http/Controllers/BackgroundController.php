<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Services\ApiRequest;
use App\Http\Controllers\Services\HostChecker;
use App\Http\Controllers\Services\Images;
use App\Http\Controllers\Services\Text;
use App\Models\Background;
use App\Models\Intro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BackgroundController extends Controller
{
    public function getContent($name)
    {
        $apiRequest = new ApiRequest();
        return $apiRequest->getSftpContent($name, 'bg', 'backgrounds');
    }

    public function tempUpload(Request $request)
    {

        if ($request->hasFile('image')) {
            $file = $request->file('image')[0];
            $textService = new Text();
            $name = str_replace(' ', '_', $file->getClientOriginalName());
            $name = str_replace(['.png', '(', ')'], '', $name);
            $bgsName = Background::get('name');
            $name = $textService->unicalCheck($bgsName, $name, 1, 2, 'name');
            Storage::disk('backgrounds')->putFileAs('', $file, $name . ".png");

            return $name;
        }
        if (isset($request['load'])){
            $name = $request['load'];
            if (Storage::disk('backgrounds')->exists($name)){
                return Storage::disk('backgrounds')->download($name);
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
        if (Storage::disk('backgrounds')->exists('/' . $file. '.png')) {
            Storage::disk('backgrounds')->delete('/' . $file. '.png');
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
        $backgrounds = Background::orderBy('created_at', 'desc')->get();

        return view('admin.backgrounds.index')->with(compact('backgrounds'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.backgrounds.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    private function imageDelete($id, $entity)
    {
        if (Storage::disk('gcs')->exists("$entity/$id")) {
            Storage::disk('gcs')->delete("$entity/$id");
        }
    }
    public function store(Request $request)
    {
        $res = redirect(route('background-index'));

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
                    $image = Storage::disk('backgrounds')->get($name.'.png');
                    $imageFunctions->pngConvertAdventures($name, 'backgrounds', $image,false);
                    Background::create([
                        'name' => $name,
                        'background' =>  '/bg/' . $name,
                    ]);
                }
            }

        return $res;

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit($id)
    {
        return Background::find($id);
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
            $name = Background::find($id)->name;
            $imageFunctions = new Images();
            $file = $imageFunctions->updateNameCleaner($request);
            $imageFunctions->pngConvertAdventures($name, 'backgrounds',  $file,false);
        }

        return redirect(route('background-index'));

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
                foreach ($model->where('background_id', $id)->get() as $item) {
                    $item->update([
                        'background_id' => null
                    ]);
                }
            }
            $bg = Background::find($id);
            $imageFunctions = new Images();

            $imageFunctions->deleteFiles($bg->name,'backgrounds');

            $bg->delete();

            return response('success', 200);
        } else {
            return response('error', 401);
        }
    }
}
