<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Services\GamesGenerate;
use App\Http\Controllers\Services\Version;
use App\Models\File;
use App\Models\Game;
use App\Models\GameDescription;
use App\Models\GameDescriptionPivot;
use App\Models\GameTemplate;
use App\Models\Learning;
use App\Models\Practice;
use App\Models\SubChapter;
use App\Models\Subtitle;
use App\Models\Theory;
use Illuminate\Http\Request;

class LearningController extends Controller
{
    public function practiceLinkGet($learning, $id)
    {
        $practice = Practice::find($id);
        $files = File::all();
        $subtitles = Subtitle::all();

        return view('admin.learnings.includes.practice-link')->with(compact('practice', 'files', 'learning','subtitles'))->render();
    }

    public function practiceLinkPost(Request $request, $learning, $id)
    {
        $practice = Practice::find($id);
        if (isset($request['files_id']) && !empty($request['files_id'])) {
            $practice->update([
                'file_id' => $request['files_id']
            ]);
        } else {
            $practice->update([
                'file_id' => null
            ]);
        }
        if (isset($request['subtitle_id']) && !empty($request['subtitle_id'])) {
            $practice->update([
                'subtitle_id' => $request['subtitle_id']
            ]);
        } else {
            $practice->update([
                'subtitle_id' => null
            ]);
        }

        return redirect(route('learning-show', $learning));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $learnings = Learning::orderBy('name')->get();

        return view('admin.learnings.index')->with(compact('learnings'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return string
     */
    public function create()
    {
        $learnings = Learning::all();
        $practices = Practice::all();
        $theories = Theory::all();

        return view('admin.learnings.includes.create')->with(compact('learnings', 'theories', 'practices'))->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $collection = Learning::create(['name' => $request->name]);

        $id = $collection->id;

        $practices = json_decode($request->practice);

        $theory = null;
        if (isset($request->theory)) {
            $theory = json_decode($request->theory);
        }

        $model = Learning::find($id);

        if (isset($theory)) {
            $model->theories()->attach($theory);
        }

        $model->practices()->attach($practices);

        $learningWithRelationship = Learning::with('theories', 'practices')->find($id);

        $gamesGenerateFunctions = new GamesGenerate();

        $wordType = false;

        foreach ($learningWithRelationship->theories as $item) {
            if (isset($item)) {
                if ($item->type == 0) {
                    $wordType = true;
                }
            }
        }

        if ($wordType) {
            $gamesGenerateFunctions->gamesAutoGenerate($learningWithRelationship, $request->game_count);
        }

        return redirect(route('learning-index'))->with(compact('id'));

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show($id)
    {
        $learning = Learning::with('practices', 'theories')->find($id);

        return view('admin.learnings.show')->with(compact('learning'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return string
     */
    public function edit($id)
    {
        $learning = Learning::with('practices', 'theories')->find($id);

        $practices = Practice::all();

        $practices = $practices->diff($learning->practices);

        $theories = Theory::all();

        $theories = $theories->diff($learning->theories);

        return view('admin.learnings.includes.edit')->with(compact('learning', 'practices', 'theories'))->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        $name = $request->name;
        $theories = null;
        if (isset($request->theory)) {
            $theories = json_decode($request->theory);
        }
        $practices = json_decode($request->practice);

        $model = Learning::find($id);

        $scene = SubChapter::where('learning_id',$id)->first();
        if (isset($scene)){
            $versionFunctions = new Version();
            $versionFunctions->versionIncrease($versionFunctions->typeGet('scene'),$scene->id);
        }

        $modelTheories = $model->theories();
        $modelPractices = $model->practices();

        $modelTheories->detach();
        $modelPractices->detach();

        if (isset($theories)) {
            $modelTheories->attach($theories);
        }
        $modelPractices->attach($practices);

        $model->update(['name' => $name]);

        return redirect(route('learning-index'))->with(compact('id'));

    }

    public function practicePageIndex($id, $learningId)
    {
        $practices = Practice::with('gameTemplates')->find($id);
        $learning = Learning::find($learningId);

        return view('admin.learnings.practice-index')->with(compact('id', 'learningId', 'practices', 'learning'));
    }

    public function practicePageEdit($id, $learningId, $practiceId)
    {
        $descriptions = GameDescription::all();

        $description = GameDescriptionPivot::where('practice_id', $practiceId)
            ->where('learning_id', $learningId)
            ->where('game_template_id', $id)->first();

        $templates = GameTemplate::find($id);

        return view('admin.learnings.includes.practice-edit')->with(compact('descriptions', 'id', 'templates', 'learningId', 'practiceId', 'description'))->render();
    }

    public function practicePageUpdate(Request $request, $id, $learningId, $practiceId)
    {
        if (isset($request->last_des)) {
            GameDescriptionPivot::where('game_template_id', $id)
                ->where('learning_id', $learningId)
                ->where('practice_id', $practiceId)
                ->where('game_description_id', $request->last_des)
                ->delete();
        }
        GameDescriptionPivot::create([
            'game_description_id' => (int)$request->description,
            'learning_id' => $learningId,
            'practice_id' => $practiceId,
            'game_template_id' => $id
        ]);
        $scene = SubChapter::where('learning_id',$learningId)->first();
        if (isset($scene)){
            $versionFunctions = new Version();
            $versionFunctions->versionIncrease($versionFunctions->typeGet('scene'),$scene->id);
        }

        return redirect(route('learning-practice', [$practiceId, $learningId]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (\Auth::check() && \Auth::user()->hasRole(['admin','qa','project','developer','methodologist'])) {
            $learning = Learning::with('games')->find($id);
            $game = $learning->games->pluck('id');
            Game::whereIn('id', $game)->delete();
            GameDescriptionPivot::where('learning_id', $id)->delete();
            $learning->delete();
            return response('success', 200);
        }
        return response('error', 401);
    }
}
