<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\ApiRequest;
use App\Http\Controllers\Services\Images;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class StatusPngPostController extends Controller
{
    public function getContent($name)
    {
        $apiRequest = new ApiRequest();
        return $apiRequest->getSftpContent($name, 'status', 'status');
    }

    public function StatusPngPost(Request $request)
    {
        if (isset($request['image'])) {
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $imageFunctions = new Images();
                $name = str_replace(' ', '_', $file->getClientOriginalName());
                $name = str_replace(['.png', '(', ')'], '', $name);
                Storage::disk('status')->putFileAs('', $file, $name . ".png");
                $imageFunctions->pngConvertAdventures($name, 'status','status', false);
                $domain = config('values.app_url');
                return "$domain/api/status/get/$name";
            } else {
                return response('it is not a file', 400);
            }
        } else
            return response('something weird', 400);
    }
}
