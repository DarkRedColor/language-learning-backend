<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Services\ApiRequest;
use App\Http\Controllers\Services\HostChecker;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FileTextController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $texts = File::where('type',0)->orderBy('created_at', 'desc')->get();

        return view('admin.files.texts.index')->with(compact('texts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return string
     */
    public function create()
    {
        return view('admin.files.texts.includes.create')->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $name = $request['name'];
        $text = $request['text'];
        $images = json_decode($request['images']);

        $text = File::create([
            'name' => $name,
            'descriptions' => $text
        ]);

        if (isset($images) && !empty($images)) {
            $apiRequest = new ApiRequest();
            $model = new File();
            foreach ($images as $key => $image) {
                $img["dd_file"] = $image;
                $apiRequest->httpPostRequestToApiStorage($text->id, $img, 'file/text', $model, false, false, false, true, $text->id . "_$key");
            }
        }
        return redirect(route('file-text-index'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return string
     */
    public function edit($id)
    {
        $text = File::find($id);
        if (isset($text->descriptions)) {
            $textsFile = json_decode($text->descriptions);
            $i = 0;
            foreach ($textsFile as $key => $item) {

                if ($item == '@@') {
                    if ($i < 2) {
                        $textsFile[$key] = '<figure class="image ck-widget ck-widget_with-resizer"><img src="' . json_decode($text->url)[$i] . '_S"></figure>';
                        $i++;
                    }
                }else{
                    $textsFile[$key] = '<p>' . $item . '</p>';

                }
            }
            $text->descriptions = $textsFile;
        }
        return view('admin.files.texts.includes.edit')->with(compact('text'))->render();
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
        $name = $request['name'];
        $text = $request['text'];
        $images = json_decode($request['images']);

        File::find($id)->update([
            'name' => $name,
            'descriptions' => $text
        ]);

        if (isset($images) && !empty($images)) {
            $apiRequest = new ApiRequest();
            $model = new File();
            foreach ($images as $key => $image) {
                $img["dd_file"] = $image;
                $apiRequest->httpPostRequestToApiStorage($id, $img, 'file/text', $model, false, false, false, true, $id . "_$key");
            }
        }
        return redirect(route('file-text-index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|int
     */
    public function destroy($id)
    {
        if (\Auth::check() && \Auth::user()->hasRole(['admin','qa','project','developer'])) {
            $hostCheck = new HostChecker();
            $host = $hostCheck->hostCheck(config('values.host'));
            Http::post(config('values.api_url') . 'file/text/delete/' . $id . '/' . $host);
            File::find($id)->delete();
            return response('success', 200);
        }
        return response('error', 401);
    }
}
