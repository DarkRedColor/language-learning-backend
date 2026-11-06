<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\Version;
use Illuminate\Http\Request;

class VersionController extends Controller
{
    public function versionApiGet()
    {
        $versionFunctions = new Version();
        $allExistentSceneVersions = \App\Models\Version::where('type',$versionFunctions->typeGet('scene'))->get();
        $sceneContentArr = null;
        foreach ($allExistentSceneVersions as $sceneVersion){
            $sceneContentArr[] = [
                'id_scene' => $sceneVersion->entity_id,
                'version' => $sceneVersion->version
            ];

        }
        $data = [
            'languages' => $versionFunctions->versionGet($versionFunctions->typeGet('languages')),
            'localization' => $versionFunctions->versionGet($versionFunctions->typeGet('text')),
            'characters' => $versionFunctions->versionGet($versionFunctions->typeGet('personages')),
            'adventure' => $versionFunctions->versionGet($versionFunctions->typeGet('adventure')),
            'music' => $versionFunctions->versionGet($versionFunctions->typeGet('music')),
            'arcade_games' => $versionFunctions->versionGet($versionFunctions->typeGet('arcade')),
            'scenes_content' => $sceneContentArr,
        ];
        return response(json_encode($data, JSON_UNESCAPED_UNICODE), 200);
    }
}
