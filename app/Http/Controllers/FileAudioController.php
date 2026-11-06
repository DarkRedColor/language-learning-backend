<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Services\ApiRequest;
use App\Http\Controllers\Services\HostChecker;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileAudioController extends Controller
{
    public function getContent($name)
    {
        $apiRequest = new ApiRequest();
        return $apiRequest->getSftpContent($name, 'file/audio', 'audio');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $audios = File::where('type', 1)->orderBy('created_at', 'desc')->get();
        $host = config('values.host');

        return view('admin.files.audios.index')->with(compact('audios', 'host'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        if ($request->hasFile('audio')) {
            $audio = $request->file('audio');
            $name = str_replace(' ', '_', $audio->getClientOriginalName());

            $apiRequest = new ApiRequest();
            $apiRequest->postSftpContent($audio,$name,'file/audio');

            File::create([
                'name' => $name,
                'url' =>  '/file/audio/' . $name,
                'type' => 1,
            ]);
        }
        return redirect(route('file-audio-index'));
    }

    public function update(Request $request, $id)
    {
        if ($request->hasFile('audio')) {
            $audio = $request->file('audio');
            $file = File::find($id);

            $apiRequest = new ApiRequest();
            $apiRequest->postSftpContent($audio,$file->name,'file/audio');
        }
        return redirect(route('file-audio-index'));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id): \Illuminate\Http\Response
    {
        if (\Auth::check() && \Auth::user()->hasRole(['admin','qa','project','developer'])) {
            $file = File::find($id);
            if (Storage::disk('gcs')->exists( '/file/audio/' . $file->name)) {
                Storage::disk('gcs')->delete( '/file/audio/' . $file->name);
            }
            $file->delete();
            return response('success', 200);
        }
        return response('error', 401);
    }
}
