<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\Emotion;

class ArrayModel extends Controller
{
    public function characterArrayGet($characterData)
    {
        $emotion =null;
        $image = new Images();
        if (isset($characterData) && !empty($characterData) && !is_bool($characterData))
            $emotion = Emotion::with('personage')->find($characterData->emotion_id);
        if (isset($emotion)) {
            $characters = [
                [
                    'id' => $emotion->personage[0]->id,
                    'position' => $characterData->position,
                    'active' => 1,
//                            $item->current,
                    'emotion_id' => $image->emotionsCatigorise($emotion->name)
                ]
            ];
        } else {
            $characters = [];
        }
        return $characters;
    }


}
