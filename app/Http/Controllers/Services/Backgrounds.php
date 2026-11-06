<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\Background;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Backgrounds extends Controller
{
    public function backgroundGet($item, $size): ?string
    {
        $background = Background::find($item->background_id) ?? null;
        $res = null;
        if (isset($background)) {
            $res = Storage::disk('gcs')->url("backgrounds/$background->name"."_$size.webp");;
        }
        return $res;
    }

    public function backgroundV2Get($item, $size, $background = null)
    {
        if (isset($item) && !empty($item) && !is_bool($item))
            if (!isset($background))
                $background = Background::find($item->background_id) ?? null;
        $res = null;
        if (isset($background)) {
            $imagesFunctions = new Images();
            $timeStamps = $background->updated_at->getTimestamp();
            $mediaUrl = Storage::disk('gcs')->url("backgrounds/$background->name"."_$size.webp");
            $res = $imagesFunctions->apiMediaModel($mediaUrl, $timeStamps, 0);
        }
        return $res;
    }
}
