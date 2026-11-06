<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Services\Version;
use App\Models\ArcadePage;
use App\Models\Emotion;
use App\Models\Language;
use App\Models\Personage;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ArcadePagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index($id)
    {
        $list = ArcadePage::where('arcade_game_id', $id)->orderBy('order')->get();

        return view('admin.arcade-games.arcade-games-list')->with(compact('list', 'id'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($type = "intro", $id = null)
    {
        $res = response('something is going wrong', 400);

        switch ($type) {
            case ('intro'):
                $res = $this->introTypeFormReturn($id);
                break;

            case ("end-game"):
                $res = $this->endGameTypeFormReturn($id);
                break;

            case ('notification'):
                $res = $this->notificationTypeFormReturn($id);
                break;

            case ("win-alert"):
                $res = $this->winAlertTypeFormReturn($id);
                break;
        }
        return $res;
    }

    private function introTypeFormReturn($id = null)
    {
        $personages = Personage::with('emotions')->get(['id', 'name_en']);
        $langs = Language::where('deleted', 1)->get(['id', 'lcid']);
        if ($id == null) {
            $res = view('admin.arcade-games.includes.intro')->with(compact(
                'langs',
                'personages',
            ))->render();
        } else {
            $page = ArcadePage::find($id);
            $res = view('admin.arcade-games.includes.intro-edit')->with(compact(
                'langs',
                'personages',
                'page',
            ))->render();
        }
        return $res;
    }

    private function winAlertTypeFormReturn($id = null)
    {
        $personages = Personage::with('emotions')->get(['id', 'name_en']);
        $langs = Language::where('deleted', 1)->get(['id', 'lcid']);
        if ($id == null) {
            $res = view('admin.arcade-games.includes.win-alert')->with(compact(
                'langs',
                'personages',
            ))->render();
        } else {
            $page = ArcadePage::find($id);
            $res = view('admin.arcade-games.includes.win-alert-edit')->with(compact(
                'langs',
                'personages',
                'page',
            ))->render();
        }
        return $res;
    }

    private function endGameTypeFormReturn($id = null)
    {
        $personages = Personage::with('emotions')->get(['id', 'name_en']);
        $langs = Language::where('deleted', 1)->get(['id', 'lcid']);
        if ($id == null) {
            $res = view('admin.arcade-games.includes.end-game')->with(compact(
                'langs',
                'personages',
            ))->render();
        } else {
            $page = ArcadePage::find($id);
            $res = view('admin.arcade-games.includes.end-game-edit')->with(compact(
                'langs',
                'personages',
                'page',
            ))->render();
        }
        return $res;
    }

    private function contentTypeReform($contentType)
    {
        switch ($contentType) {
            case('intro'):
                $contentType = 0;
                break;
            case ('notification'):
                $contentType = 1;
                break;
            case ('end-game'):
                $contentType = 2;
                break;
            case ('choice'):
                $contentType = 3;
                break;
            case ('win-alert'):
                $contentType = 4;
                break;
        }
        return $contentType;
    }

    private function buildArrayData($request): array
    {
        $personage_id = null;
        if (isset($request->emotion_id)) {
            $personage_id = Emotion::with('personage')->find($request->emotion_id)->personage[0]->id;
        }
        $data['type'] = $this->contentTypeReform($request->content_type);
        $data['emotion_id'] = $request->emotion_id;
        $data['arcade_game_id'] = $request->sub_chapter_id;
        $data['personage_id'] = $personage_id;
        $data['place'] = $request->place ?? null;
        $data['add_type'] = $request->add_type ?? null;
        $position = 0;
        if ($request->position === "right") {
            $position = 1;
        }
        $data['position'] = $position;

        return $data;
    }

    private function introAndEndGameStoreFunction($request, $id)
    {
        $lineOrActionData = $this->buildArrayData($request);
        $pages = ArcadePage::where('arcade_game_id', $request->sub_chapter_id)->orderBy('order')->get();
        $versionFunctions = new Version();
        $versionFunctions->versionIncrease($versionFunctions->typeGet('arcade'),$request->sub_chapter_id);

        [$keys, $values] = Arr::divide($request->all());
        for ($i = 5; $i < count($keys); $i++) {
            $lineOrActionData [$keys[$i]] = $values[$i];
        }
        return $this->extracted($id, $pages, $lineOrActionData, $request);
    }

    private function winAlertStoreFunction($request, $id)
    {
        $data = $this->buildArrayData($request);
        $pages = ArcadePage::where('arcade_game_id', $request->sub_chapter_id)->orderBy('order')->get();
        $versionFunctions = new Version();
        $versionFunctions->versionIncrease($versionFunctions->typeGet('arcade'),$request->sub_chapter_id);

        [$keys, $values] = Arr::divide($request->all());
        for ($i = 7; $i < count($keys); $i++) {
            $data[$keys[$i]] = $values[$i];
        }
        return $this->extracted($id, $pages, $data, $request);
    }

    private function notificationStoreFunction($request, $id)
    {
        $notificationData['type'] = $this->contentTypeReform($request->content_type);
        $notificationData['arcade_game_id'] = $request->sub_chapter_id;
        $pages = ArcadePage::where('arcade_game_id', $request->sub_chapter_id)->orderBy('order')->get();
        $versionFunctions = new Version();
        $versionFunctions->versionIncrease($versionFunctions->typeGet('arcade'),$request->sub_chapter_id);

        [$keys, $values] = Arr::divide($request->all());
        for ($i = 3; $i < count($keys); $i++) {
            $notificationData [$keys[$i]] = $values[$i];
        }
        return $this->extracted($id, $pages, $notificationData, $request);
    }

    private function notificationTypeFormReturn($id = null): string
    {
        $langs = Language::where('deleted', 1)->get(['id', 'lcid']);

        if ($id == null) {
            $res = view('admin.arcade-games.includes.notification')->with(compact(
                'langs',
            ))->render();
        } else {
            $page = ArcadePage::find($id);
            $res = view('admin.arcade-games.includes.notification-edit')->with(compact(
                'langs',
                'page',
            ))->render();
        }
        return $res;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id = null)
    {
        $res = response("something is going wrong", 400);
        switch ($request->content_type) {
            case ('intro'):
            case ('end-game'):
                $res = $this->introAndEndGameStoreFunction($request, $id);
                break;

            case ('notification'):
                $res = $this->notificationStoreFunction($request, $id);
                break;

            case ('win-alert'):
                $res = $this->winAlertStoreFunction($request, $id);
                break;
        }

        return $res;
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
            $page = ArcadePage::find($id);
            $page->delete();
            $pages = ArcadePage::where('arcade_game_id',$page->arcade_game_id)->orderBy('order')->get();
            foreach ($pages as $key => $item){
                ArcadePage::find($item->id)->update([
                    'order' => $key
                ]);
            }

            return response('success', 200);
        }
        return response('error', 401);
    }

    private function extracted($id, $pages, array $lineOrActionData, $request)
    {
        if ($id == null) {
            $lineOrActionData['order'] = count($pages);
            $record = ArcadePage::create($lineOrActionData);
            $id = $record->id;
        } else {
            ArcadePage::find($id)->update($lineOrActionData);
        }
        return redirect(route('arcade-games-list', $request->sub_chapter_id))->with(compact('id'));
    }
}
