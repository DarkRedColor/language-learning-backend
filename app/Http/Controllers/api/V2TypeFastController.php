<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\Api;
use App\Http\Controllers\Services\ArcadeGames;
use App\Http\Controllers\Services\Images;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class V2TypeFastController extends Controller
{
    public function arcadeGamesGet(Request $request)
    {
        $apiFunctions = new Api();
        $image = new Images();
        $arcadeGamesFunctions = new ArcadeGames();

        $web = 0;
        if (array_key_exists('web',$request->all())){
            $web = $request->web;
        }

        if (array_key_exists('lcid', $request->all())) {
            $lcid = $request->lcid;
            if ($apiFunctions->lcidExistingCheck($lcid,$web) <> $lcid) {
                return response('bad lcid', 400);
            }
        } else {
            $lcid = "none";
        }

        $validator = Validator::make($request->all(), [
            'icons_scale' => 'required',
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            return response('not enough params', 400);
        }

        $type = $request->type;
        $size = $image->iconScaleCheck($request->icons_scale);


        return $arcadeGamesFunctions->dataGet($type,$size,$lcid);
    }
}
