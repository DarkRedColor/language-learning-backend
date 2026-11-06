<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FormatChecker extends Controller
{
    public function pngFormat()
    {
        return 'data:image/png;base64,';
    }

    public function jpgFormat()
    {
        return 'data:image/jpg;base64,';
    }

    public function jpegFormat()
    {
        return 'data:image/jpeg;base64,';
    }

    public function formatCheck($image)
    {
        $format = $this->pngFormat();
        if (Str::contains($image, $this->pngFormat())) {
            $format = $this->pngFormat();
        } elseif (Str::contains($image, $this->jpegFormat())) {
            $format = $this->jpegFormat();

        } elseif (Str::contains($image, $this->jpgFormat())) {
            $format = $this->jpgFormat();
        }
        return $format;
    }
}
