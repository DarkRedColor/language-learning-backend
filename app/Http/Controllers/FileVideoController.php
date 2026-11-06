<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Services\ApiRequest;
use App\Http\Controllers\Services\HostChecker;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FileVideoController extends Controller
{
    public function getContent($name)
    {
        $apiRequest = new ApiRequest();
        return $apiRequest->getSftpContent($name, 'file/video', 'video');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $videos = File::where('type', 2)->orderBy('created_at', 'desc')->get();

        return view('admin.files.videos.index')->with(compact('videos',));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        if ($request->hasFile('video')) {

            $video = $request->file('video');
            $name = str_replace(' ', '_', $video->getClientOriginalName());

            $apiRequest = new ApiRequest();
             $apiRequest->postSftpContent($video,$name,'file/video');

            File::create([
                'name' => $name,
                'url' =>  '/file/video/' . $name,
                'type' => 2,
            ]);
        }
        return redirect(route('file-video-index'));
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
        if (isset($request['video'])){
            if ($request->hasFile('video')){
                $file = File::find($id);
                $video = $request->file('video');

                $apiRequest = new ApiRequest();
                $apiRequest->postSftpContent($video,$file->name,'file/video');
            }
        }
        return redirect(route('file-video-index'));
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
            if (Storage::disk('gcs')->exists( '/file/video/' . $file->name)) {
                Storage::disk('gcs')->delete( '/file/video/' . $file->name);
            }
            $file->delete();
            return response('success', 200);
        }
        return response('error', 401);
    }
}
