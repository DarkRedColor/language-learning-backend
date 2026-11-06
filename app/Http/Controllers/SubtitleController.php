<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Services\CrudText;
use App\Models\Subtitle;
use Illuminate\Http\Request;
use App\Models\Language;


class SubtitleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $texts = Subtitle::orderBy('name_en')->get();
        $languages = Language::where('deleted', 1)->get();


        return view('admin.subtitles.index')->with(compact('texts', 'languages'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return string
     */
    public function create()
    {
        $languages = Language::where('deleted', 1)->get();

        return view('admin.subtitles.create')->with(compact('languages'))->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $model = new Subtitle();

        $id = $model->create([
            'key' => $request['key'],
            'name_en' => $request['name_en']
        ]);
//        $crud = new CrudText();
//        $id = $crud->store($request, $model);

        return redirect(route('subtitle-index'))
            ->with(compact('id'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return string
     */
    public function edit($id)
    {
        $text = Subtitle::find($id);
        $languages = Language::where('deleted', 1)->get();
        if (isset($text->name_en)) {
            $textsFile = json_decode($text->name_en);
            if ($textsFile <> null) {
                $i = 0;
                foreach ($textsFile as $key => $item) {

                    if ($item == '@@') {
                        if ($i < 2) {
                            $textsFile[$key] = '<figure class="image ck-widget ck-widget_with-resizer"><img src="' . json_decode($text->url)[$i] . '_S"></figure>';
                            $i++;
                        }
                    } else {
                        $textsFile[$key] = '<p>' . $item . '</p>';

                    }
                }
                $text->name_en = $textsFile;
            } else {
                $text->name_en = ['<p>' . $text->name_en . '</p>'];
            }
        }

        return view('admin.subtitles.edit')
            ->with(compact('text', 'languages'))->render();
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
        $model = new Subtitle();
        $name = $request['key'];
        $text = $request['name_en'];

        $model->find($id)->update([
            'key' => $name,
            'name_en' => $text
        ]);
        return redirect(route('subtitle-index'))
            ->with(compact('id'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        {
            if (\Auth::check() && \Auth::user()->hasRole(['admin', 'qa', 'project', 'developer'])) {
                Subtitle::find($id)->delete();

                return response('success', 200);
            }
            return response('error', 401);
        }
    }
}
