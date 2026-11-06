<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Services\ApiRequest;
use App\Http\Controllers\Services\HostChecker;
use App\Http\Controllers\Services\Language;
use App\Http\Controllers\Services\Version;
use App\Models\Season;
use App\Models\SubChapter;
use App\Models\Theory;
use App\Models\TheoryPage;
use App\Models\TheoryPageName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use function GuzzleHttp\Promise\all;

class TheoryPagesController extends Controller
{
    private $title = 0;
    private $description = 1;
    private $example = 2;
    private $image = 3;
    private $table = 4;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index($id)
    {
        $pagesName = TheoryPageName::where('theory_id', $id)->get();
        $theory = Theory::find($id);

        return view('admin.theory-pages.index')->with(compact('pagesName', 'theory'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return string
     */
    public function create($theoryId)
    {
        return view('admin.theory-pages.includes.create')->with(compact('theoryId'))->render();
    }

    public function createLocalization(Request $request, $theoryId, $theoryPageStackId, $language = null)
    {
        $ids = $request['ids'];
        $languages = (new Language())->getActiveLanguage();
        $cards = TheoryPage::where('theory_page_name_id', $theoryPageStackId)->orderBy('order')->get();
        return view('admin.theory-pages.includes.localisation')->with(compact('language', 'theoryId', 'theoryPageStackId', 'ids', 'languages', 'cards'))->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|false|string
     */
    public function store($theoryId, Request $request)
    {
        $cards = json_decode($request['cards-input']);

        $arr[] = [];
        $theoryPageStack = TheoryPageName::create([
            'theory_id' => $theoryId,
        ]);
        $scene = SubChapter::where('theory_id',$theoryId)->first();
        if (isset($scene)){
            $versionFunctions = new Version();
            $versionFunctions->versionIncrease($versionFunctions->typeGet('scene'),$scene->id);
        }
        $fieldLocalisation = "text_" . $request->language;
        foreach ($cards as $item) {
            $arr = $this->getArr($item, $arr, $fieldLocalisation);
            $arr[$item->index]['theory_page_name_id'] = $theoryPageStack->id;
        }
        $stackId['stack_id'] = $theoryPageStack->id;
        $ids = [];
        foreach ($arr as $key => $item) {
            if ($item['type'] <> $this->image) {
                $arr[$key][$fieldLocalisation] = json_encode($item[$fieldLocalisation]);
            }
//            else {
//                $arr[$key]['image'] = json_encode($item['image']);
//            }
            $theoryPage = TheoryPage::create($arr[$key]);
            $ids[] = $theoryPage->id;
        }
        $stackId['ids'] = $ids;
        $images = TheoryPage::wherein(
            'id', $ids)
            ->where('type', $this->image)->get();

        if ($request->dd_file <> null) {
            foreach ($images as $image) {
                $this->apirequest($request, $image->id);
            }
        }

        return $stackId;
    }

    private function multipleTextFieldBuild($arr, $index, $item, $fieldLocalisation)
    {
        if (isset($arr[$index][$fieldLocalisation])) {
            $arr[$index][$fieldLocalisation][] = $item->content;
        } else {
            $arr[$index][$fieldLocalisation] = [$item->content];
        }
        return $arr[$index][$fieldLocalisation];
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return string
     */
    public function edit($theoryId, $id)
    {
        $cards = TheoryPage::where('theory_page_name_id', $id)->orderBy('order')->get();

        return view('admin.theory-pages.includes.edit')->with(compact('cards', 'theoryId'))->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function update(Request $request)
    {
        $cards = json_decode($request['cards-input']);
        $arr[] = [];
        $fieldLocalisation = "text_" . $request->language;
        foreach ($cards as $item) {
            $arr = $this->getArr($item, $arr, $fieldLocalisation);
            $arr[$item->index]['theory_page_name_id'] = $request["theoryPageStack"];

        }
        $stackId['stack_id'] = $request["theoryPageStack"];
        $ids = [];
        $theoryPages = TheoryPage::where('theory_page_name_id', $request["theoryPageStack"])->get();
        $lastElemOrder = 0;
        foreach ($arr as $key => $item) {
            $lastElemOrder = $item['order'];
            $theoryPage = $theoryPages->where('order', $item['order'])->first();

            if ($item['type'] <> $this->image) {
                $arr[$key][$fieldLocalisation] = json_encode($item[$fieldLocalisation]);
            }
//            else {
//                $arr[$key]['text_en'] = json_encode($item['image']);
//            }
            if (isset($theoryPage)) {
                $theoryPage->update($arr[$key]);
            } else {
                $theoryPage = TheoryPage::create($arr[$key]);
            }
            $ids[] = $theoryPage->id;
        }
        if (count($theoryPages) > count($arr)) {
            foreach ($theoryPages as $item) {
                if ($item->order > $lastElemOrder) {
                    $item->delete();
                }
            }
        }
        $stackId['ids'] = $ids;

        $images = TheoryPage::wherein(
            'id', $ids)->where(
            'type', $this->image)->get();

        if ($request->dd_file <> null) {
            foreach ($images as $image) {
                $this->apirequest($request, $image->id);
            }
        }

        return $stackId;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (\Auth::check() && \Auth::user()->hasRole(['admin','qa','project','developer','methodologist'])) {

            $pages = TheoryPage::where('theory_page_name_id', $id)->get();

            $image = $pages->where('type', $this->image)->first();

            if (isset($image)) {
                $hostCheck = new HostChecker();
                $host = $hostCheck->hostCheck(config('values.host'));
                Http::post(config('values.api_url') . 'theory/delete/' . $image->id . '/' . $host);
            }

            $ids = $pages->pluck('id');

            TheoryPage::wherein('id', $ids)->delete();

            TheoryPageName::find($id)->delete();
            return response('success', 200);
        }
        return response('error', 401);
    }

    public function getArr($item, array $arr, string $fieldLocalisation): array
    {
        switch ($item->type) {
            case 'title-card':
                $arr[$item->index][$fieldLocalisation] = $this->multipleTextFieldBuild($arr, $item->index, $item, $fieldLocalisation);
                $arr[$item->index]['type'] = $this->title;
                break;
            case 'description-card':
                $arr[$item->index][$fieldLocalisation] = $this->multipleTextFieldBuild($arr, $item->index, $item, $fieldLocalisation);
                $arr[$item->index]['type'] = $this->description;
                break;

            case 'example-card':
                $arr[$item->index][$fieldLocalisation] = $this->multipleTextFieldBuild($arr, $item->index, $item, $fieldLocalisation);
                $arr[$item->index]['type'] = $this->example;
                break;
            case 'image-card':
//                $arr[$item->index]['image'] = [$item->content];
                $arr[$item->index]['type'] = $this->image;
                break;
            default:
                $arr[$item->index][$fieldLocalisation] = $this->multipleTextFieldBuild($arr, $item->index, $item, $fieldLocalisation);
                $arr[$item->index]['type'] = $this->table;
        }
        $arr[$item->index]['order'] = $item->index;
        return $arr;
    }

    private function apirequest($request, $id)
    {
        $apiRequest = new ApiRequest();
        $response = $apiRequest->imagesRequestDataFill($request->dd_file, $id, 'theory');
        $hostCheck = new HostChecker();
        $host = $hostCheck->hostCheck(config('values.host'));

        if ($response->status() == 200) {
            TheoryPage::where('id', $id)->update([
                'image' => config('values.api_url') . 'theory/' . $host . '/' . $id,
            ]);
        }
    }
}