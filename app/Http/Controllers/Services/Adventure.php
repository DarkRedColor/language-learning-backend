<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class Adventure extends Controller
{
    public function lockedOrUnlockedEntity($model, $id, $isClosed = true)
    {
        $model->where('id', $id)->update([
            'is_displayed_to_user' => $isClosed,
        ]);
    }

    public function entityOrderSet($model, $listOfIds)
    {
        foreach ($listOfIds as $key => $item) {
            $model->where('id', $item)->update([
                'order' => $key
            ]);

        }
    }
}
