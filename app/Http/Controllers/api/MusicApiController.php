<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;


class MusicApiController extends Controller
{
    public function getContent()
    {
        $name = 'Lanlgy_music.zip';
        if (Storage::disk('gcs')->exists("$name")) {
            return Storage::disk('gcs')->url("$name");
        } elseif (Storage::disk('gcs')->exists("music/$name")) {
            Storage::disk('gcs')
                ->put($name, Storage::disk('gcs')
                    ->get("music/$name"));
            return Storage::disk("gcs")->url("music/$name");
        }else
            return response('something is wrong', 400);


    }
}


















































