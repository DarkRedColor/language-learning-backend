<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\HostChecker;
use App\Http\Controllers\Services\Images;
use App\Http\Controllers\Services\Version;
use App\Models\Intro;
use App\Models\Language;
use App\Models\Personage;
use App\Models\SubChapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class V2CharacterApiController extends Controller
{
    private $versions = [
        'v2' => true,
    ];

    public function getAllCharacters(Request $request, $version)
    {
        if ($this->versions[$version]) {
            $charactersData = [];
            $ids = null;
            $characters = null;
            if (array_key_exists('icons_scale', $request->all())) {
                $image = new Images();
                $size = $image->iconScaleCheck($request->icons_scale);
            } else return response('bad icons_scale', 400);

            if (array_key_exists('sub-chapters', $request->all())) {
                $subChapters = 'sub-chapters';

                $ids = explode(',', $request->$subChapters);

            }

            if (isset($ids)) {
                foreach ($ids as $item) {
                    if ($item == '-1') {
                        $characters = Personage::with('emotions')->get();
                    } else {
                        $dialogue = SubChapter::where('sub_chapter_id', $item)->first();
                        if (isset($dialogue)) {
                            $dialoguePages = Intro::where('sub_chapter_id', $dialogue->id)->get();
                            if (isset($dialoguePages)) {
                                foreach ($dialoguePages as $page) {
                                    $persIds[] = $page->personage_id;
                                }
                            }

                        }
                    }
                }

                if (isset($persIds))
                    $charactersArr[] = Personage::with('emotions')->find(array_unique($persIds));
                if (isset($charactersArr)) {
                    foreach ($charactersArr as $item) {
                        if (count($item) > 0) {
                            foreach ($item as $pers) {
                                $characters[] = $pers;

                            }
                        }
                    }
                }
            } else {
                $characters = Personage::with('emotions')->get();
            }
            $lcids = (new \App\Http\Controllers\Services\Language())->getActiveLanguage();

            if (isset($characters) && !empty($characters)) {
                foreach ($characters as $key => $item) {
                    $namesArray = [];
                    foreach ($lcids as $lcid) {
                        $localisateField = "name_$lcid->lcid";
                        $namesArray[] = [
                            'lcid' => $lcid->lcid,
                            'value' => $item->$localisateField ?? null
                        ];
                    }
                    $charactersData [] = [
                        'id' => $item->id,
                        'name' => $namesArray,

                    ];

                    $emotions = [];
                    if (isset($item->emotions)) {
                        foreach ($item->emotions as $emotion) {
                            $imagesFunctions = new Images();
                            $timeStamps = $emotion->updated_at->getTimestamp();
                            $mediaUrl = Storage::disk('gcs')->url('emotions/' . $emotion->name . '_' . $size . '.webp');
                            $emotionsArray = [
                                'type' => $image->emotionsCatigorise($emotion->name),
                                'image' => $imagesFunctions->apiMediaModel($mediaUrl, $timeStamps, 0)
                            ];
                            $emotions[] = $emotionsArray;
                        }
                    }
                    $charactersData[$key] += [
                        'default_emotions' => $emotions
                    ];
                }
            }

            return response($charactersData, 200);
        } else {
            return response('something wrong', 400);
        }
    }
}
