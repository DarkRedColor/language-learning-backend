<?php

namespace App\Http\Controllers;

use App\Exports\DialogueExport;
use App\Exports\TextsExport;
use App\Http\Controllers\Services\ApiRequest;
use App\Http\Controllers\Services\HostChecker;
use App\Http\Controllers\Services\Version;
use App\Imports\DialogueImport;
use App\Imports\NewDialogueImport;
use App\Models\Animation;
use App\Models\AnimationEmotion;
use App\Models\Background;
use App\Models\Emotion;
use App\Models\Intro;
use App\Models\Language;
use App\Models\Personage;
use App\Models\SubChapter;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;


class DialoguePagesController extends Controller
{
    public function download($filename)
    {
        if (file_exists(public_path() . '/dialogues/' . $filename)) {
            return response()->download(public_path() . '/dialogues/' . $filename);
        } else {
            return redirect()->back();
        }
    }

    public function getContent($name)
    {
        $apiRequest = new ApiRequest();
        return $apiRequest->getSftpContent($name, 'dialogue/audio', 'dialogue/audio');
    }

    public function exportGet($id)
    {
        $languages = (new Services\Language())->getActiveLanguage();

        return view('admin.dialogue-pages.export')->with(compact('languages', 'id'));
    }

    public function export(Request $request, $id)
    {
        $arr = $request->all();
        $dialogue = SubChapter::find($id);
        if (count($arr) > 1) {
            exec('rm -r ' . public_path() . '/dialogues');
            \File::makeDirectory(public_path() . '/dialogues', 0777, false, true);
            foreach ($arr as $item) {
                $languages = Language::where('lcid', $item)->first();

                $name = Str::replace(' ', '_', $dialogue->name_en . "_" . $languages->display_name . "(" . $languages->lcid . ').xlsx');

                Excel::store(new DialogueExport([$item], $id), $name, 'export_dialogues_uploads');
            }
            $filePathAndName = public_path() . "/dialogues/" . $dialogue->name_en . "_" . collect($arr)->implode('_') . ".zip";
            $zip = new \ZipArchive();
            $zip->open($filePathAndName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
            $path = public_path('dialogues');
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
            foreach ($files as $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    // extracting filename with substr/strlen
                    $relativePath = 'dialogues/' . substr($filePath, strlen($path) + 1);

                    $zip->addFile($filePath, $relativePath);
                }
            }
            $zip->close();
            return $dialogue->name_en . "_" . collect($arr)->implode('_') . ".zip";
        } else {
            $export = new DialogueExport([$arr[0]], $id);
            $languages = Language::where('lcid', $arr[0])->first();
            $name = Str::replace(' ', '_', $dialogue->name_en . "_" . $languages->display_name . "(" . $languages->lcid . ').xlsx');
            Excel::store($export, $name, 'export_dialogues_uploads');
            return $name;
        }
    }

    public function import(Request $request)
    {
        if ($request->hasFile("import-file")) {
            $file = $request->file("import-file");
            Excel::import(new DialogueImport(), $file);
            $res = redirect()->back()->with('success', 200);
        } else {
            $res = redirect()->back()->with('failed', 400);
        }
        return $res;
    }

    public function newDialogueImport(Request $request, $id)
    {
        if ($request->hasFile("import-new-file")) {
            $file = $request->file("import-new-file");
            Excel::import(new NewDialogueImport($id), $file);
            $res = redirect()->back()->with('success', 200);
        } else {
            $res = redirect()->back()->with('failed', 400);
        }
        return $res;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index($chapterId, $parentId = null)
    {
        $parent = null;
        $arr = [];
        $namesOfElements = [];
        if ($parentId <> null) {
            $parent = Intro::find($parentId);
            $arr = $this->arrayFormation($parentId, $parent, $arr);
            $namesOfElements = $this->buildArrayOfNamesOfElements($parentId, $parent, $namesOfElements);
        }

        $pages = Intro::where('parent_id', $parentId)->where('sub_chapter_id', $chapterId)->orderBy('order')->get();
        $namesOfElements = array_reverse($namesOfElements);
        $arr = array_reverse($arr, false);
        $subChapter = SubChapter::find($chapterId);
        return view('admin.dialogue-pages.index')->with(compact(
            'pages',
            'chapterId',
            'parentId',
            'parent',
            'arr',
            'namesOfElements',
            'subChapter',
        ));
    }

    private function arrayFormation($id, $parent, $arr)
    {
        $parentElement = $parent->find($id);
        if (isset($parentElement->parent_id)) {
            $arr [] = $parentElement->parent_id;
            $arr = $this->arrayFormation($parentElement->parent_id, $parent, $arr);
        } else {
            $arr [] = null;
        }
        return $arr;
    }

    private function buildArrayOfNamesOfElements($id, $parent, $arr)
    {
        $parentElement = $parent->find($id);
        $arr [] = $this->switchDialogueGet($parentElement);
        if (isset($parentElement->parent_id)) {
            $arr = $this->buildArrayOfNamesOfElements($parentElement->parent_id, $parent, $arr);
        }
        return $arr;
    }

    private function switchDialogueGet($parentElement)
    {
        $type = null;
        switch ($parentElement->content_type) {
            case(0):
                $type = $parentElement->order + 1 . "L";
                break;
            case (1):
                $type = $parentElement->order + 1 . "A";
                break;
            case (2):
                $type = $parentElement->order + 1 . "N";
                break;
            case (3):
                $type = $parentElement->order + 1 . "C";
                break;
        }
        return $type;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return string
     */
    public function create($type = "line", $id = null)
    {
        $res = response('something is going wrong', 400);

        switch ($type) {
            case ('line'):
                $res = $this->lineTypeFormReturn($id);
                break;

            case ("action"):
                $res = $this->actionTypeFormReturn($id);
                break;

            case ('notification'):
                $res = $this->notificationTypeFormReturn($id);
                break;

            case ("choice"):
                $res = $this->choiceTypeFormReturn($id);
                break;
        }
        return $res;

    }

    private function lineTypeFormReturn($id = null): string
    {
        $personages = Personage::with('emotions')->get(['id', 'name_en', 'voice']);
        $bg = Background::get(['id', 'name']);
        $langs = Language::where('deleted', 1)->get(['id', 'lcid']);

        if ($id == null) {
            $res = view('admin.dialogue-pages.includes.line-create')->with(compact(
                'langs',
                'bg',
                'personages',
            ))->render();
        } else {
            $page = Intro::find($id);
            $res = view('admin.dialogue-pages.includes.line-edit')->with(compact(
                'langs',
                'bg',
                'personages',
                'page',
            ))->render();
        }
        return $res;

    }

    private function actionTypeFormReturn($id = null): string
    {
        $personages = Personage::with('emotions')->get(['id', 'name_en']);
        $bg = Background::get(['id', 'name']);
        $langs = Language::where('deleted', 1)->get(['id', 'lcid']);

        if ($id == null) {
            $animations = Animation::orderBy('created_at', 'desc')->get();
            foreach ($animations as $item) {
                $animations->find($item->id)['animation'] = AnimationEmotion::where('animation_id', $item->id)->get();
            }
            $res = view('admin.dialogue-pages.includes.action-create')->with(compact(
                'langs',
                'bg',
                'personages',
                'animations'
            ))->render();
        } else {
            $page = Intro::find($id);
            $animations = Animation::orderBy('created_at', 'desc')->get();
            foreach ($animations as $item) {
                $animations->find($item->id)['animation'] = AnimationEmotion::where('animation_id', $item->id)->get();
            }
            $res = view('admin.dialogue-pages.includes.action-edit')->with(compact(
                'langs',
                'bg',
                'personages',
                'page',
                'animations',
            ))->render();
        }
        return $res;
    }

    private function choiceTypeFormReturn($id = null): string
    {
        $personages = Personage::with('emotions')->get(['id', 'name_en']);
        $bg = Background::get(['id', 'name']);

        if ($id == null) {
            $res = view('admin.dialogue-pages.includes.choice-create')->with(compact(
                'bg',
                'personages',
            ))->render();
        } else {
            $page = Intro::find($id);
            if ($page->text_en <> null) {
                $langs = Language::where('deleted', 1)->get(['id', 'lcid']);

                $res = view('admin.dialogue-pages.includes.sub-choice-edit')->with(compact(
                    'langs',
                    'page',
                ))->render();
            } else {
                $res = view('admin.dialogue-pages.includes.choice-edit')->with(compact(
                    'bg',
                    'personages',
                    'page',
                ))->render();
            }

        }
        return $res;
    }

    private function notificationTypeFormReturn($id = null): string
    {
        $bg = Background::get(['id', 'name']);
        $langs = Language::where('deleted', 1)->get(['id', 'lcid']);

        if ($id == null) {
            $res = view('admin.dialogue-pages.includes.notification-create')->with(compact(
                'langs',
                'bg',
            ))->render();
        } else {
            $page = Intro::find($id);
            $res = view('admin.dialogue-pages.includes.notification-edit')->with(compact(
                'langs',
                'bg',
                'page',
            ))->render();
        }
        return $res;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(Request $request, $id = null)
    {
        $res = response("something is going wrong", 400);
        switch ($request->content_type) {
            case ('action'):
            case ('line'):
                $res = $this->lineAndActionStoreFunction($request, $id);
                break;

            case ('notification'):
                $res = $this->notificationStoreFunction($request, $id);
                break;

            case ('choice'):
                $res = $this->choiceStoreFunction($request, $id);
                break;
        }

        return $res;
    }

    private function contentTypeReform($contentType)
    {
        switch ($contentType) {
            case('line'):
                $contentType = 0;
                break;
            case ('action'):
                $contentType = 1;
                break;
            case ('notification'):
                $contentType = 2;
                break;
            case ('choice'):
                $contentType = 3;
                break;
        }
        return $contentType;
    }

    private function positionGet($request)
    {
        $position = 0;
        if ($request->position === "right") {
            $position = 1;
        }
        return $position;
    }

    private function buildArrayData($request)
    {
        $personage_id = null;
        if (isset($request->emotion_id)) {
            $personage_id = Emotion::with('personage')->find($request->emotion_id)->personage[0]->id;
        }
        $lineOrActionData['content_type'] = $this->contentTypeReform($request->content_type);
        $lineOrActionData['sub_chapter_id'] = $request->sub_chapter_id;
        $lineOrActionData['emotion_id'] = $request->emotion_id;
        $lineOrActionData['personage_id'] = $personage_id;
        $lineOrActionData['background_id'] = $request->background_id;
        $lineOrActionData['parent_id'] = $request->parent_id;

        return $lineOrActionData;
    }

    private function lineAndActionStoreFunction($request, $id)
    {
        $lineOrActionData = $this->buildArrayData($request);
        $pages = Intro::where('sub_chapter_id', $lineOrActionData['sub_chapter_id'])->where('parent_id', $request->parent_id)->orderBy('id')->get();

        [$keys, $values] = Arr::divide($request->all());
        for ($i = 8; $i < count($keys); $i++) {
            $lineOrActionData [$keys[$i]] = $values[$i];
        }
        $position = $this->positionGet($request);
        $lineOrActionData['position'] = $position;

        return $this->extracted($id, $pages, $lineOrActionData, $request);
    }

    private function orderSet($request)
    {
        if (isset($request->tree)) {
            $tree = json_decode($request->tree);

            foreach ($tree as $key => $item) {
                if (count($tree) - 2 < $key) {
                    $page = Intro::find($item);
                    if ($page->content_type <> 3) {
                        $page->update([
                            'order' => $key,
                            'next_id' => null,
                        ]);
                    } else {
                        $page->update([
                            'order' => $key,
                        ]);
                    }

                } else {
                    $page = Intro::find($item);
                    if ($page->content_type <> 3) {
                        $next_key = $key + 1;
                        $page->update([
                            'order' => $key,
                            'next_id' => [["$tree[$next_key]"]]

                        ]);
                    } else {
                        $page->update([
                            'order' => $key,
                        ]);
                    }
                }

            }
        }
    }

    private function notificationStoreFunction($request, $id)
    {
        $notificationData['content_type'] = $this->contentTypeReform($request->content_type);
        $notificationData['sub_chapter_id'] = $request->sub_chapter_id;
        $notificationData['background_id'] = $request->background_id;
        $notificationData['parent_id'] = $request->parent_id;
        $pages = Intro::where('sub_chapter_id', $notificationData['sub_chapter_id'])->where('parent_id', $request->parent_id)->orderBy('id')->get();
        [$keys, $values] = Arr::divide($request->all());
        for ($i = 6; $i < count($keys); $i++) {
            $notificationData [$keys[$i]] = $values[$i];
        }
        return $this->extracted($id, $pages, $notificationData, $request);
    }

    private function  choiceStoreFunction($request, $id)
    {
        $choiceData = $this->buildArrayData($request);
        $pages = Intro::where('sub_chapter_id', $choiceData['sub_chapter_id'])->where('parent_id', $request->parent_id)->orderBy('id')->get();
        if ($id == null) {
            $choiceData['order'] = count($pages);
            $item = Intro::create($choiceData);
            $id = $item->id;
            $dialogue = SubChapter::find($item->sub_chapter_id);
            if (isset($dialogue->sub_chapter_id)) {
                $versionFunctions = new Version();
                $versionFunctions->versionInit($versionFunctions->typeGet('scene'), $id);
            }
            $lastCreatedChoice = Intro::orderBy('created_at', 'desc')->first();
            $parent = Intro::find($request->parent_id);
            if (isset($parent)) {
                $next_ids = $parent->next_id;
                $next_ids[] = ["$lastCreatedChoice->id"];
                $parent->update([
                    'next_id' => $next_ids
                ]);
            }
            if (count($pages) <> 0) {
                if ($pages[count($pages) - 1]->content_type <> 3) {
                    Intro::find($pages[count($pages) - 1]->id)->update([
                        'next_id' => [["$lastCreatedChoice->id"]]
                    ]);
                }
            }
            $next_ids = null;
            for ($i = 0; $i < $request->variants; $i++) {
                Intro::create([
                    'sub_chapter_id' => $choiceData['sub_chapter_id'],
                    'order' => $i,
                    'parent_id' => $lastCreatedChoice->id,
                    'text_en' => 'Choice ' . ($i + 1),
                    'content_type' => $choiceData['content_type']
                ]);

            }
            $aux = Intro::where('parent_id', $lastCreatedChoice->id)->where('content_type', 3)->get();
            foreach ($aux as $value) {
                $next_ids[] = ["$value->id"];
            }
            Intro::find($lastCreatedChoice->id)->update([
                'next_id' => $next_ids,
            ]);

        } else {
            [$keys, $values] = Arr::divide($request->all());
            for ($i = 8; $i < count($keys); $i++) {
                $choiceData [$keys[$i]] = $values[$i];
            }
            $item = Intro::find($id);
            $position = $this->positionGet($request);
            $choiceData['position'] = $position;
            $item->update($choiceData);
            $dialogue = SubChapter::find($item->sub_chapter_id);
            if (isset($dialogue->sub_chapter_id)) {
                $versionFunctions = new Version();
                $versionFunctions->versionIncrease($versionFunctions->typeGet('scene'), $id);
            }
            $this->orderSet($request);

        }

        return redirect(route('dialogue-page-index', [$request->sub_chapter_id, $request->parent_id]))->with(compact('id'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (\Auth::check() && \Auth::user()->hasRole(['admin', 'qa', 'project', 'developer'])) {
            $page = Intro::find($id);
            $subChapterId = $page->sub_chapter_id;
            $pages = Intro::where('sub_chapter_id', $subChapterId)->whereNull('parent_id')->orderBy('order')->get();
            $deletedPageNextId = $page->next_id;
            $previousPage = null;

            if ($page->order <> 0) {
                $previousPage = $pages->where('order', $page->order - 1)->first();

            }
            if ($previousPage <> null) {
                Intro::find($previousPage->id)->update([
                    'next_id' => $deletedPageNextId,
                ]);
            }

            if ($page->content_type == 3) {
                foreach ($page->next_id as $item) {
                    $this->recursiveDeleteForChoice($item);
                }
                $page->delete();
            }

            Intro::where('id', $id)->delete();
            $pages = Intro::where('sub_chapter_id', $subChapterId)->whereNull('parent_id')->orderBy('order')->get();

            foreach ($pages as $key => $item) {
                $item->update(['order' => $key]);
                dump($item, $key);
                $data = $this->cascadeDelete($item, $id);
                if ($data <> null) {
                    Intro::where('id', $item->id)->update(['next_id' => $data]);
                }
            }
            return response('success', 200);
        }
        return response('error', 401);
    }

    private function recursiveDeleteForChoice($page)
    {
        if (isset($page)) {
            foreach ($page as $item) {
                $elem = Intro::find($item);
                if (isset($elem)) {
                    if ($elem->content_type == 3) {
                        if ($elem->next_id <> null) {
                            foreach ($elem->next_id as $values) {
                                $this->recursiveDeleteForChoice($values);
                            }
                        }
                        $elem->delete();
                    } else {
                        $elem->delete();
                    }
                }
            }
        }
    }

    private function cascadeDelete($intros, $id)
    {
        $next_id = $intros['next_id'] ?? null;
        if (isset($next_id)) {
            foreach ($next_id as $key => $item) {
                if ($item[0] == $id) {
                    unset($next_id[$key]);
                }
            }
        }
        return $next_id;
    }

    private function extracted($id, $pages, array $lineOrActionData, $request)
    {
        $apiRequest = new ApiRequest();
        if ($id == null) {
            $lineOrActionData['order'] = count($pages);
            if (isset($lineOrActionData['voice'])) {
                $lineOrActionData['voice'] = 1;
            } else {
                $lineOrActionData['voice'] = 0;
            }
            $model = Intro::create($lineOrActionData);
            $id = $model->id;
            $dialogue = SubChapter::find($model->sub_chapter_id);
            if (isset($dialogue->sub_chapter_id)) {
                $versionFunctions = new Version();
                $versionFunctions->versionInit($versionFunctions->typeGet('scene'), $id);
            }
            if ($request->hasFile('voice')) {
                $audio = $request->file('voice');
                $nameWord = $id . '.mp3';
                $apiRequest->postSftpContent($audio, $nameWord, 'dialogue/audio');
                Intro::find($id)->update([
                    'voice' => 1
                ]);
            }
            $parent = Intro::find($request->parent_id);
            if (isset($parent)) {
                $next_ids = $parent->next_id;
                $next_ids[] = ["$id"];
                $parent->update([
                    'next_id' => $next_ids
                ]);
            }
            if (count($pages) <> 0) {
                if ($pages[count($pages) - 1]->content_type <> 3) {
                    Intro::find($pages[count($pages) - 1]->id)->update([
                        'next_id' => [["$id"]]
                    ]);
                }
            }
        } else {
            $model = Intro::find($id);
            $dialogue = SubChapter::find($model->sub_chapter_id);
            if (isset($dialogue->sub_chapter_id)) {
                $versionFunctions = new Version();
                $versionFunctions->versionIncrease($versionFunctions->typeGet('scene'), $id);
            }
            if ($request->hasFile('voice')) {
                $audio = $request->file('voice');
                $nameWord = $id . '.mp3';
                $apiRequest->postSftpContent($audio, $nameWord, 'dialogue/audio');
                $model->update([
                    'voice' => 1
                ]);
            }
            $deleted = $request->is_deleted;
            if ($deleted <> null && $request->hasFile('voice') == null) {
                $hostCheck = new HostChecker();
                $host = $hostCheck->hostCheck(config('values.host'));
                if (Storage::disk('sftp')->exists($host . '/dialogue/audio/' . $id . '.mp3')) {
                    Storage::disk('sftp')->delete($host . '/dialogue/audio/' . $id . '.mp3');
                }
                if (Storage::disk('dialogue/audio')->exists($id . '.mp3')) {
                    Storage::disk('dialogue/audio')->delete($id . '.mp3');
                }
                $model->update([
                    'voice' => 0
                ]);
            }
            unset($lineOrActionData['voice']);

            $model->update($lineOrActionData);
            $this->orderSet($request);
        }
        return redirect(route('dialogue-page-index', [$request->sub_chapter_id, $request->parent_id]))->with(compact('id'));
    }
}
