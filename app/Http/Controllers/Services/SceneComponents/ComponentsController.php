<?php

namespace App\Http\Controllers\Services\SceneComponents;

use App\Http\Controllers\api\PracticeApiController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\Dialogues;
use App\Http\Controllers\Services\Info;
use App\Http\Controllers\Services\IntroductionWelcome;
use App\Http\Controllers\Services\Language;
use App\Models\Chapter;
use App\Models\DialogueJson;
use App\Models\GamesJson;
use App\Models\InfoJson;
use App\Models\Learning;
use App\Models\Motivation;
use App\Models\MotivationJson;
use App\Models\SubChapter;

class ComponentsController extends Controller
{
    private function languagesGet()
    {
        $langs = new Language();
        return $langs->getActiveLanguage();
    }

    private $sizes = ['S', 'M', 'L'];

    private function info()
    {
        return new Info();

    }

    private function welcomAndIntroductionModel()
    {
        return new IntroductionWelcome();
    }

    public function infoData()
    {
        $scenes = SubChapter::whereNotNull('chapter_id')->get();

        $info = $this->info();
        if (isset($scenes)) {
            foreach ($scenes as $scene) {
                $dialogue = SubChapter::where('sub_chapter_id', $scene->id)->first();
                $datas = null;
                $languages = $this->languagesGet();

                foreach ($languages as $language) {
                    $data = null;
                    foreach ($this->sizes as $size) {
                        $data[$size] = $info->infoDataBuild($scene, $dialogue, $language->lcid, $size);
                    }
                    $datas[$language->lcid] = json_encode($data);
                }
                $model = new InfoJson();
                $collection = $model->create($datas);
                $model->find($collection->id)->subChapters()->attach($scene->id);
            }
        }
        return 'finish';
    }

    public function infoUpdate($id)
    {
        $scene = SubChapter::with('infoJson')->find($id);
        $info = $this->info();
        if (isset($scene)) {
            $dialogue = SubChapter::where('sub_chapter_id', $scene->id)->first();
            $datas = null;
            $languages = $this->languagesGet();
            foreach ($languages as $language) {
                $data = null;
                foreach ($this->sizes as $size) {
                    $data[$size] = $info->infoDataBuild($scene, $dialogue, $language->lcid, $size);
                }
                $datas[$language->lcid] = json_encode($data);
                $model = new InfoJson();
                if (isset($scene->infoJson)) {
                    $model->find($scene->infoJson[0]->id)->update($datas);
                } else {
                    $collection = $model->create($datas);
                    $model->find($collection->id)->subChapters()->attach($scene->id);
                }
            }
        }
        return 'finish';
    }

    public function dialogueData()
    {
        $scenes = SubChapter::whereNotNull('sub_chapter_id')->get();
        $dialogueDataBuild = new Dialogues();
        $languages = $this->languagesGet();

        if (isset($scenes)) {
            foreach ($scenes as $dialogue) {
                $datas = null;
                $id = $dialogue->sub_chapter_id;
                foreach ($languages as $language) {
                    $data = null;
                    $start = now();
                    foreach ($this->sizes as $size) {
                        $firstOneId = $dialogueDataBuild->getTheFirstOneId($dialogue->id);
                        $frames = $dialogueDataBuild->framesDataBuild($dialogue->id);
                        $default_frames_data = $dialogueDataBuild->dialogueDefaultFramesDataBuild($dialogue->id, $size, $language->lcid);
                        $choices_frames_data = $dialogueDataBuild->dialogueChoiceFramesDataBuild($dialogue->id, $size, $language->lcid);
                        $data[$size] = [
                            'first_frame_id' => $firstOneId,
                            'frames' => $frames,
                            'default_frames_data' => $default_frames_data,
                            'choices_frames_data' => $choices_frames_data,
                        ];
                    }
                    $time = $start->diffInSeconds(now());
                    echo $language->lcid . ' processed in ' . $time . " \n";
                    $datas[$language->lcid] = json_encode($data);
                }
                $model = new DialogueJson();
                $collection = $model->create($datas);
                $model->find($collection->id)->subChapters()->attach($id);
            }
        }
        return ' finish';


    }

    public function gameData()
    {
        $randomLocalise = rand(0, 1);
        $learnings = Learning::with('practices', 'games')->get();
        $languages = $this->languagesGet();
        $practiceFunctions = new PracticeApiController();

        foreach ($learnings as $learning){
            $datas = null;
            foreach ($languages as $language) {
                $data = null;
                $start = now();
                foreach ($this->sizes as $size) {
                    $data[$size] = $practiceFunctions->practiceGet($learning, $size, $language->lcid, $randomLocalise);
                }
                $time = $start->diffInSeconds(now());
                echo $language->lcid . ' processed in ' . $time . " \n";
                $datas[$language->lcid] = json_encode($data);
            }
            $model = new GamesJson();
            $collection = $model->create($datas);
            $model->find($collection->id)->learnings()->attach($learning->id);
        }
        return ' finish';
    }

//    public function motivationData()
//    {
//        $motivations = new Motivation();
//        $introductionWelcomeFunctions = $this->welcomAndIntroductionModel();
//        $scenes = SubChapter::whereNotNull('chapter_id')->get();
//
//        if (isset($scenes)) {
//            foreach ($scenes as $scene) {
//                $chapter = Chapter::find($scene->chapter_id);
//                $datas = null;
//                $languages = $this->languagesGet();
//                foreach ($languages as $language) {
//                    $data = null;
//                    foreach ($this->sizes as $size) {
//                        $data[$size] = $introductionWelcomeFunctions->DataBuild($scene, $language->lcid, $size, $motivations, $chapter->type, 2);
//                    }
//                    $datas[$language->lcid] = json_encode($data);
//                }
//                $model = new MotivationJson();
//                $collection = $model->create($datas);
//                $model->find($collection->id)->motivations()->attach($scene->id);
//            }
//        }
//        return 'finish';
//
//    }

//    public function motivationUpdate($id)
//    {
//        $motivations = new Motivation();
//        $introductionWelcomeFunctions = $this->welcomAndIntroductionModel();
//        $scene = SubChapter::with('infoJson')->find($id);
//        if (isset($scene)) {
//            $chapter = Chapter::find($scene->chapter_id);
//            $datas = null;
//            $languages = $this->languagesGet();
//            foreach ($languages as $language) {
//                $data = null;
//                foreach ($this->sizes as $size) {
//                    $data[$size] = $introductionWelcomeFunctions->DataBuild($scene, $language->lcid, $size, $motivations, $chapter->type, 2);
//                }
//                $datas[$language->lcid] = json_encode($data);
//            }
//            $model = new MotivationJson();
//            if (isset($scene->infoJson)) {
//                $model->find($scene->infoJson[0]->id)->update($datas);
//            } else {
//                $collection = $model->create($datas);
//                $model->find($collection->id)->subChapters()->attach($scene->id);
//            }
//        }
//        return 'finish';
//    }
}