<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Episode;
use App\Models\SubChapter;
use Illuminate\Http\Request;

class AdventuresController extends Controller
{
    public function adventuresCoordinatesPost(Request $request)
    {
        $adventuresArray = $request->all();
        if (isset($adventuresArray)) {
            foreach ($adventuresArray as $season) {
                if (isset($season)) {
                    foreach ($season['episodes'] as $episode) {
                        if (isset($episode)) {
                            if (isset($episode['transform'])) {
                                $episodeId = $episode['id'];
                                Episode::find($episodeId)->update([
                                    'transform' => $episode['transform'],
                                ]);
                            }
                            foreach ($episode['chapters'] as $chapter) {
                                if (isset($chapter)) {
                                    if (isset($chapter['transform'])) {
                                        $chapterId = $chapter['id'];
                                        Chapter::find($chapterId)->update([
                                            'transform' => $chapter['transform'],
                                        ]);
                                    }
                                    $sceneType = 'scenes';
                                    if (array_key_exists('intro_scenes', $chapter)) {
                                        $sceneType = 'intro_scenes';
                                    } elseif (array_key_exists('quiz_scenes', $chapter)) {
                                        $sceneType = 'quiz_scenes';
                                    } elseif (array_key_exists('default_scenes', $chapter)) {
                                        $sceneType = 'default_scenes';
                                    }

                                    foreach ($chapter[$sceneType] as $scene) {
                                        if (isset($scene)) {
                                            if (isset($scene['transform'])) {
                                                $sceneId = $scene['id'];
                                                SubChapter::find($sceneId)->update([
                                                    'transform' => $scene['transform'],
                                                ]);
                                            }

                                        }

                                    }

                                }

                            }

                        }

                    }

                }

            }

        }
        return response('alles ist gut', 200);

    }
}
