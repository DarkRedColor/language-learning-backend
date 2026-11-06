<?php

namespace App\Http\Controllers\Services\Parser;

use App\Http\Controllers\Controller;
use App\Models\TheoryPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Image extends Controller
{
    public function imageDataGet($id, $size)
    {
        $theoryPage = TheoryPage::find($id);
        $res = null;
        if (isset($theoryPage)) {
            $res = Storage::disk('gcs')->url("theory/$theoryPage->id" . "_$size.webp");
        }
        return $res;
    }
}
