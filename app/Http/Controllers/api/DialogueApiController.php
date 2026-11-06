<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\Api;
use App\Models\Background;
use App\Models\Chapter;
use App\Models\Dialogue;
use App\Models\Personage;
use App\Models\SubChapter;

class DialogueApiController extends Controller
{
    private function sizeImg($size)
    {
        $res = $size . ".png";
        if ($size == "ipad" || $size == '.png') {
            $res = (new Api())->twoX();
        }

        if ($size == "2x" || $size == "2x.png" || $size == (new Api())->twoX()) {
            $res = (new Api())->twoX();
        }

        if ($size == "3x" || $size == "3x.png" || $size == (new Api())->treeX()) {
            $res = (new Api())->treeX();
        }

        return  $res;
    }

    public function getDialogue($id, $iso, $size = '_2x.png')
    {
        $dialogue = Dialogue::where('sub_chapter_id', $id)
            ->whereNotNull('order')
            ->orderBy('order')
            ->get([
                'id',
                'text_en',
                'text_' . $iso,
                'position',
                'order',
                'current',
                'background_id',
                'personage_id',
                'emotion_id',
                'sub_chapter_id'
            ])
            ->makeHidden(['created_at', 'updated_at'])
            ->toArray();

        if (count($dialogue) === 0) {

            return response('no content', 204);

        } else {
            $datas = [];

            foreach ($dialogue as $key => $item) {
                $data = [];
                $info = [];
                $pers = Personage::find($item['personage_id']);
                $emotion = $pers->emotions()->where('id', $item['emotion_id'])->get();
                $info['name'] = $pers['name'];
                $info['current'] = $item['current'];
                $info['position'] = $item['position'];
                $info['image'] = $this->emotionNullCheker($pers,$size,$emotion);
                $data['characters'] = [$info];
                if ($item['background_id'] == null) {
                    $subchapterId = SubChapter::find($item['sub_chapter_id']);
                    if ($subchapterId == null) {
                        return response('', 204);
                    }
                    $chapter = Chapter::find($subchapterId['chapter_id']);
                    $bg = Background::find($chapter['background_id']);

                    $data['background'] = $bg->background . (new Api)->sizeBg($size);

                } else {
                    $bg = Background::find($item['background_id']);
                    $data['background'] = $bg->background . (new Api)->sizeBg($size);
                }

                $data['text_en'] = $item['text_en'];
                $data['text_translate'] = $item['text_' . $iso];
                $data['content_type'] = 1;
                $data['order'] = $key;
                $data['game_type'] = 22;
                $datas [] = $data;
            }
            return response(json_encode($datas, JSON_UNESCAPED_UNICODE));
        }
    }

    private function emotionNullCheker($pers, $size, $emotion)
    {
        if (isset($emotion)) {
            $info['image'] = $pers['image'] . $this->sizeImg($size);
        } else {
            if ($emotion[0]['image'] == null) {
                $info['image'] = $pers['image'] . $this->sizeImg($size);
            } else {
                $info['image'] = $emotion[0]['image'] . $this->sizeImg($size);
            }
        }
        return $info['image'];
    }
}
