<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\Api;
use App\Http\Controllers\Services\HostChecker;
use App\Models\Background;
use App\Models\Chapter;
use App\Models\Emotion;
use App\Models\Intro;
use App\Models\Language;
use App\Models\Personage;
use App\Models\SubChapter;
use Illuminate\Http\Request;

class IntroApiController extends Controller
{
    public function introGet(Request $request)
    {
        $size = 'L';
        if (array_key_exists('icons_scale', $request->all())) {
            $size = $request->icons_scale;
        }
        $hostCheck = new HostChecker();
        $host = $hostCheck->hostCheck(config('values.host'));
        $apiFunctions = new Api();
        $intro = Chapter::find(0);
        $data = [];
        $data['name'] = $this->localiseTextGet($intro, 'name_') ?? null;
        $data['locked'] = $apiFunctions->imgNullChck($intro, $apiFunctions->sizesConvert($size), 'image_locked', 'chapter/image/' . $host . "/") ?? null;
        $data['unlocked'] = $apiFunctions->imgNullChck($intro, $apiFunctions->sizesConvert($size), 'image_unlocked', 'chapter/image/' . $host . "/") ?? null;
        $data['scenes'] = $this->scene(0, $size, $host, $apiFunctions);

        return json_encode($data);

    }

    private function scene($id, $size, $host, $apiFunctions)
    {
        $datas = [];
        $scenes = SubChapter::where('chapter_id', $id)->orderBy('created_at')->get();

        foreach ($scenes as $item) {
            $data['id'] = (int)$item->id;
            $data['tags'] = $item->tags;
            $data['locked'] = $apiFunctions->imgNullChck($item, $apiFunctions->sizesConvert($size), 'image_locked', 'sub-chapter/image/' . $host . "/") ?? null;
            $data['unlocked'] = $apiFunctions->imgNullChck($item, $apiFunctions->sizesConvert($size), 'image_unlocked', 'sub-chapter/image/' . $host . "/") ?? null;
            $data['pages'] = $this->scenePages($item) ?? null;
            $datas [] = $data;
        }
        return $datas;
    }

    private function scenePages($scene)
    {
        $datas = [];
        $pages = Intro::where('sub_chapter_id', $scene->id)->get();
        foreach ($pages as $item) {
            if ($item->content_type == 3 && $item->parent_id <> null) {
                continue;
            } else {
                $data ['id'] = $item->id ?? null;
                $data ['characters'] = $this->charactersGet($item) ?? null;
                $data ['background'] = $this->backgroundGet($item->background_id) ?? null;
                $data ['text'] = $this->localiseTextGet($item) ?? null;
//                $data ['text_translate'] = null;
                $data ['content_type'] = $item->content_type ?? null;
                $data ['order'] = $item->order ?? null;
                $data ['parent_id'] = $item->parent_id ?? null;
                $data ['next'] = $this->nextGet($item)[0] ?? null;
                $datas [] = $data;
            }
        }
        return $datas;
    }

    private function backgroundGet($id)
    {
        $href = null;
        if (isset($id) && !empty($id)) {
            $bg = Background::find($id);
            $href = $bg->background ?? null;
        }
        return $href;

    }

    private function charactersGet($item)
    {
        $data = null;
        $id = null;

        if (isset($item) && !empty($item)) {
            if ($item->emotion_id <> null && $item->emotion_id <> 0) {
                $char = Emotion::find($item->emotion_id);
                if (isset($char) && !empty($char)) {
                    $id = $char->personage_id ?? null;
                }
            } else {
                $char = Personage::find($item->personage_id);
                if (isset($char) && !empty($char)) {
                    $id = $char->id ?? null;
                }
            }
            if (isset($char) && !empty($char)) {
                $data ['id'] = $id;
                $name = Personage::find($item->personage_id);
                $data ['name'] = $name->name_en ?? null;
                $data ['current'] = $item->current ?? null;
                $data ['position'] = $item->position ?? null;
                $data ['image'] = $char->image ?? null;
            }
        }
        return [$data];
    }

    private function localiseTextGet($item, $field = 'text_')
    {
        $langs = Language::where('deleted', 1)->get();
        $datas = [];
        if (isset($item) && !empty($item)) {
            foreach ($langs as $lang) {
                $localiseText = $field . $lang->lcid;
                $data['lcid'] = $lang->lcid ?? null;
                $data['value'] = $item->$localiseText ?? null;
                $datas [] = $data;
            }
        }
        return $datas;
    }

    public function nextGet($item, $dialogues = null)
    {
        $datas = null;

        if (isset($item->next_id) && !empty($item->next_id)) {
            foreach ($item->next_id as $id) {
                if (isset($id) && !empty($id)) {
                    if (array_key_exists($id[0], $dialogues)) {
                        $page = $dialogues[$id[0]];
                    } else
                        $page = $dialogues[$id[0]] = Intro::find($id[0]);
                    if (isset($page->content_type)) {
                        if ($page->content_type == 3) {
                            [$datas,$dialogues] = $this->nextChoiceFrameIdGet($page, $dialogues);
//                            $data['name'] = $this->localiseTextGet($page) ?? null;
//                            $data['type'] = 0;//$this->nextChoiceFrameTypeGet($this->nextChoiceFrameIdGet($page));
//                            $datas [] = $data;
                        } else {
                            $datas = (int)$item->next_id[0][0];
//                            $data['name'] = $this->localiseTextGet($page) ?? null;
//                            $data['type'] = 0;
//                            $datas [] = $data;
                        }
                    }
                }
            }
        } else {
            if ($item->parent_id <> null) {
//                $page = Intro::find($this->nextChoiceFrameIdGet($item)) ?? null;

                [$datas,$dialogues] = $this->nextChoiceFrameIdGet($item,$dialogues);
//                $data['name'] = $this->localiseTextGet($page) ?? null;
//                $data['type'] = 0;
//                $datas[] = $data;
            }
        }

        return [$datas, $dialogues];
    }


    private function nextChoiceFrameIdGet($page, $dialogues = null)
    {
        $nextFrameId = $page->next_id ?? null;
        if ($nextFrameId <> null) {
            if ($page->content_type == 3 && $page->parent_id <> null) {
                $nextFrameId = $nextFrameId[0][0];
            } else {
                $nextFrameId = (int)$page->id;
            }

        } else {
            if (array_key_exists($page->parent_id, $dialogues)) {
                $parent = $dialogues[$page->parent_id];
            } else {
                $parent = $dialogues[$page->parent_id] = Intro::find($page->parent_id);
            }

            if (isset($parent) && !empty($parent)) {
                if ($parent->parent_id <> null) {
                    if (array_key_exists($parent->parent_id, $dialogues)) {
                        $parent = $dialogues[$parent->parent_id];
                    } else {
                        $parent = $dialogues[$parent->parent_id] = Intro::find($parent->parent_id);
                    }
                }
                $nextChoiceFrame = Intro::where('parent_id', null)
                    ->where('order', $parent->order + 1)
                    ->where('sub_chapter_id', $parent->sub_chapter_id)
                    ->first();
                $nextFrameId = $nextChoiceFrame->id ?? null;
            }
        }
        return [(int)$nextFrameId,$dialogues];
    }

    private function nextChoiceFrameTypeGet($id)
    {
        $type = null;
        if (isset($id) && !empty($id)) {
            $page = Intro::find($id) ?? null;
            $type = $page->content_type ?? null;
        }

        return $type;
    }

}
