<?php

namespace App\Http\Controllers;

use App\Exports\TextsExport;
use App\Http\Controllers\Services\CrudText;
use App\Http\Controllers\Services\Version;
use App\Imports\TextsImport;
use App\Models\Language;
use App\Models\Text;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class TextController extends Controller
{
    public function download($filename)
    {
        if (file_exists(public_path() . '/text_interface/' . $filename)) {
            return response()->download(public_path() . '/text_interface/' . $filename);
        } else {
            return redirect()->back();
        }
    }

    public function textPreview(Request $request)
    {

        $lcids = $request->all();
        $arr = [];
        foreach ($lcids as $key => $item) {
            $arr[$key]['lcid'] = $item;
            $arr[$key]['words'] = Text::whereNull('name_' . $item)->get('name_en')->toArray();
        }
        return view('admin.texts.includes.preview')->with(compact('arr'))->render();
    }

    public function exportGet()
    {
        $languages = (new Services\Language())->getActiveLanguage();

        return view('admin.texts.export')->with(compact('languages'));
    }

    public function export(Request $request)
    {
        $arr = $request->all();
        if (count($arr) > 1) {
            exec('rm -r ' . public_path() . '/text_interface');
            \File::makeDirectory(public_path() . '/text_interface', 0777, false, true);

            foreach ($arr as $item) {
                $languages = Language::where('lcid', $item)->first();
                $name = Str::replace(' ', '_', 'English_to_' . $languages->display_name . '_(en_' . $languages->lcid . ').xlsx');

                Excel::store(new TextsExport([$item]), $name, 'export_uploads');
            }

            $filePathAndName = public_path() . "/text_interface/" . 'Text_interface_'.collect($arr)->implode('_') . ".zip";
            $zip = new \ZipArchive();
            $zip->open($filePathAndName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
            $path = public_path('text_interface');
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
            foreach ($files as $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    // extracting filename with substr/strlen
                    $relativePath = 'text_interface/' . substr($filePath, strlen($path) + 1);

                    $zip->addFile($filePath, $relativePath);
                }
            }
            $zip->close();
            return 'Text_interface_'.collect($arr)->implode('_') . ".zip";
//            return response()->download($filePathAndName);

        } else {
            $export = new TextsExport([$arr[0]]);
            $languages = Language::where('lcid', $arr[0])->first();
            $name = Str::replace(' ', '_', 'Text_Interface_English_to_' . $languages->display_name . '_(en_' . $languages->lcid . ').xlsx');
            Excel::store($export, $name, 'export_uploads');
            return $name;
        }


    }

    public function import(Request $request)
    {
        if ($request->hasFile("import-file")) {
            $file = $request->file("import-file");
            Excel::import(new TextsImport(), $file);
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
    public function index()
    {
        $texts = Text::orderBy('name_en')->get();
        $languages = Language::where('deleted', 1)->get();

        return view('admin.texts.index')->with(compact('texts', 'languages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return string
     */
    public function create()
    {
        $languages = Language::where('deleted', 1)->get();

        return view('admin.texts.create')->with(compact('languages'))->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $model = new Text();
        $crud = new CrudText();
        $id = $crud->store($request, $model);
        $versionFunctions = new Version();
        $versionFunctions->versionInit($versionFunctions->typeGet('text'),$id);

        return redirect(route('text-index'))
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
        $text = Text::find($id);

        $languages = Language::where('deleted', 1)->get();


        return view('admin.texts.edit')
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
        $model = new Text();
        $crud = new CrudText();
        $id = $crud->update($request, $id, $model);
        $versionFunctions = new Version();
        $versionFunctions->versionIncrease($versionFunctions->typeGet('text'),$id);
        return redirect(route('text-index'))
            ->with(compact('id'));
    }

    public function textSearch(Request $request)
    {
        if ($request['text'] == 'all') {
            $texts = Text::orderBy('name_en')->get();
        } else {
            $texts = Text::where('name_en', 'LIKE', $request['text'] . "%")->orderBy('name_en')->get();
        }

        return view('admin.texts.includes.search-results')->with(compact('texts'))->render();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (\Auth::check() && \Auth::user()->hasRole(['admin','qa','project','developer'])) {
            Text::where('id', $id)->delete();

            return response('success', 200);
        }
        return response('error', 401);
    }
}
