<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Version extends Controller
{
    public function versionInit($type, $id = null)
    {
        if ($type == 5) {
            $res = \App\Models\Version::create([
                'type' => $type,
                'entity_id' => $id
            ]);
        }else{
            $res = \App\Models\Version::create([
                'type' => $type,
            ]);
        }
        return $res;
    }

    public function versionIncrease($type, $id = null)
    {
        $model = new \App\Models\Version();
        if ($type==5) {
            $versionColl = $model->where('type', $type)
                ->where('entity_id', $id)
                ->first();
        }else{
            $versionColl = $model->where('type', $type)
                ->first();
        }
        if (isset($versionColl)) {
            $version = $versionColl->version + 1;
        } else {
            $this->versionInit($type, $id);
            $version = 0;
        }if ($type == 5) {
        $model->where('type', $type)
            ->where('entity_id', $id)
            ->update([
                'version' => $version
            ]);
    }else{
        $model->where('type', $type)
            ->update([
                'version' => $version
            ]);
    }
        return $version;
    }

    public function typeGet($type)
    {
        $typeArr = [
            'languages' => 1,
            'text' => 2,
            'personages' => 3,
            'arcade' => 4,
            'scene' => 5,
            'adventure' =>6,
            'music' => 7
        ];
        return $typeArr[$type] ?? null;
    }
    public function versionGet($type, $id = null)
    {
        $model = new \App\Models\Version();
        if ($type == 5) {
            $versionColl = $model->where('type', $type)
                ->where('entity_id', $id)
                ->first();
        }else{
            $versionColl = $model->where('type', $type)
                ->first();
        }
        if (isset($versionColl)) {
            $version = $versionColl->version;
        } else {
            $this->versionInit($type, $id);
            $version = 0;
        }
        return $version;
    }
}
