<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Str;

class Words extends Controller
{
    public function list($lists, $languages)
    {
        $list[0] = [];
        $list[1] = [];
        $list[2] = [];
        $list[3] = [];
        $i = 0;
        $j = 0;

        foreach ($lists as $item) {
            $nullable = [];
            foreach ($languages as $language) {
                if ($language->lcid === "en") {
                    continue;
                }
                $description_ = "meaning_" . $language->lcid;
                if (!$item->$description_ == null) {
                    $list[1][$j] = '';
                    $i += 1;
                } else {
                    $nullable [] = $language->lcid;
                }
                $list[0][$j] = $i;
            }
            $nullable = json_encode($nullable, JSON_UNESCAPED_UNICODE);
            $nullable = Str::replace(['"', '[', ']'], '', $nullable);
            $list[1][$j] = $nullable;
            $j += 1;
            $i = 0;
        }
        return $list;
    }
}
