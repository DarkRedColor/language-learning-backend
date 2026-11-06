<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Services\ApiRequest;
use App\Http\Controllers\Services\HostChecker;
use App\Http\Controllers\Services\Images;
use App\Http\Controllers\Services\Models;
use App\Http\Controllers\Services\Version;
use App\Models\DisplayLocalisationMessage;
use App\Models\Game;
use App\Models\GameDescription;
use App\Models\Intro;
use App\Models\Language;
use App\Models\Text;
use App\Models\Theory;
use App\Models\TheoryPage;
use App\Models\Word;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;


class LanguageController extends Controller
{
    public function getContent($name)
    {
        $apiRequest = new ApiRequest();
        return $apiRequest->getSftpContent($name, 'language/icon', 'language');
    }
    public function notDisplayed($id)
    {
        Language::where('id', $id)->update([
            'is_displayed_to_user' => false,
        ]);
    }
    public function displayed($id, $confirm = null)
    {
        $res = 'success';
        if ($confirm <> null) {
            Language::where('id', $id)->update([
                'is_displayed_to_user' => true,
            ]);
        } else {
            $language = Language::find($id);
            if ($language->name <> null && $language->learn <> null && $language->my_language <> null && $language->flag <> null) {
                Language::where('id', $id)->update([
                    'is_displayed_to_user' => true,
                ]);
            } else{
                $res = "error";
            }
        }

        return $res;
    }
    public function notDisplayedWeb($id)
    {
        Language::where('id', $id)->update([
            'is_displayed_to_user_web' => false,
        ]);
    }
    public function displayedWeb($id, $confirm = null)
    {
        $res = 'success';
        if ($confirm <> null) {
            Language::where('id', $id)->update([
                'is_displayed_to_user_web' => true,
            ]);
        } else {
            $language = Language::find($id);
            if ($language->name <> null && $language->learn <> null && $language->my_language <> null && $language->flag <> null) {
                Language::where('id', $id)->update([
                    'is_displayed_to_user_web' => true,
                ]);
            } else{
                $res = "error";
            }
        }

        return $res;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function languagesList()
    {
        $lenguageList = Language::where('status', 1)
            ->get(['name']);


        return response(json_encode($lenguageList));

    }

    public function index()
    {
        $languages = Language::orderBy('display_name', 'asc')->get();

        return view('admin.languages.admin.index')->with(compact('languages'));
    }

    public function mainMenuIndex()
    {
        $languages = Language::where('deleted', 1)->orderBy('display_name', 'asc')
            ->get();
        $hostCheck = new HostChecker();
        $host = $hostCheck->hostCheck(config('values.host'));

        return view('admin.languages.index')->with(compact('languages','host'));

    }


    /**
     * Show the form for creating a new resource.
     *
     * @return string
     */
    public function create()
    {

        return view('admin.languages.admin.includes.create-modal')->render();
    }

    public function mainMenuCreate()
    {
        $languages = Language::where('deleted', 0)->orderBy('display_name', 'asc')->get();

        return view('admin.languages.create')->with(compact('languages'))->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $request->validate([
            'display_name' => 'required|max:100',
//            'title' => $this->validationMax(),
            'lcid' => 'required|unique:languages|max:15',
//            'learn' => $this->validationMax(),
//            'my_language' => $this->validationMax(),
        ]);
        $apiServices = new Services\ApiRequest();

        $apiServices->languageCreateOrUpdate($request, true, false,null);

        (new Services\Language)->langCreate($request);


        $word = new Word();
        $word->addNewLanguage($request['lcid']);

        return redirect(route('language-index'))
            ->with('success', 'Language created successfully');
    }

    public function mainMenuStore(Request $request)
    {
        $language = Language::find($request->id);

        (new Services\Language)->langCreate($language);

        $word = new Word();
        $word->addNewLanguage($language['lcid']);

        Language::where('id', $request->id)->update([
            'deleted' => 1,
        ]);
        $versionFunctions = new Version();
        $versionFunctions->versionInit($versionFunctions->typeGet('languages'),$request->id);

        return redirect(route('main-language-index'))
            ->with('success', 'Language created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit($id)
    {
        $language = Language::find($id);

        return view('admin.languages.admin.includes.edit-modal')
            ->with(compact('language',));
    }

    public function mainMenuEdit($id)
    {
        $language = Language::find($id);
        $hostCheck = new HostChecker();
        $host = $hostCheck->hostCheck(config('values.host'));

        return view('admin.languages.edit')->with(compact('language','host'))->render();
    }

    public function updateLang(Request $request, $id)
    {
        $apiServices = new Services\ApiRequest();
        $apiServices->languageCreateOrUpdate($request, false, $id, null);
        $versionFunctions = new Version();
        $versionFunctions->versionIncrease($versionFunctions->typeGet('languages'),$id);
        return redirect(route('language-index'))
            ->with('success', 'language is updated successfully');
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
        $apiServices = new Services\ApiRequest();
        $time = time();

        $language = Language::find($id);
        if ($request->hasFile('flag')) {
            $file = $request->file('flag');
            $filePath = "/language/icon/$id.png";
            Storage::disk('gcs')->put($filePath, file_get_contents($file)); // test version, not finished
        }
        $apiServices->languageCreateOrUpdate($request, false, $id, $file->getClientOriginalName());

//        if ($request->dd_file <> null) {
//            $image = $request->dd_file;
//            $host = config('values.host');
//            $apiUrl = config('values.api_url');
//            $data = [
//                'file' => $image,
//                'iso' => $language->lcid,
//                'host' => $host,
//                'time' => $time,
//            ];
//
//            $response = Http::post($apiUrl . 'language', [
//                $data
//            ]);
//
//            if ($response->status() == 200) {
//
//                $name = $apiUrl . 'language/' . (new Services\HostChecker)->hostCheck($host) . '/' . $language->lcid;
//                $apiServices->languageCreateOrUpdate($request, false, $id, $name);
//            } else {
//                return redirect(route('main-language-index'))
//                    ->with('error', $response->body());
//            }
//        } else {
//
//            $apiServices->languageCreateOrUpdate($request, false, $id, "none");
//        }
        $versionFunctions = new Version();
        $versionFunctions->versionIncrease($versionFunctions->typeGet('languages'),$id);
        return redirect(route('main-language-index'))
            ->with('success', 'language is updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        if (\Auth::check() && \Auth::user()->hasRole(['admin','qa','project','developer'])) {
            $lang = Language::where('id', $id)->firstOrFail();

            if ($lang['lcid'] == "en") {
                return response('fail', 406);
            } else {

                $models = new Models();

                $models->deleteLanguage($lang['lcid'], "seasons");

                $models->deleteLanguage($lang['lcid'], "episodes");

                $models->deleteLanguage($lang['lcid'], "chapters");

                $models->deleteLanguage($lang['lcid'], "display_localisation_messages");

                $models->deleteLanguage($lang['lcid'], "sub_chapters");

                $game = new Game();
                $game->deleteLanguage($lang['lcid']);

                $text = new Text();
                $text->deleteLanguage($lang['lcid']);

                $theoryPages = new TheoryPage();
                $theoryPages->deleteLanguage($lang['lcid']);

                $gameDescription = new GameDescription();
                $gameDescription->deleteLanguage($lang['lcid']);

                $word = new Word();
                $word->deleteLanguage($lang['lcid']);

                $theory = new Theory();
                $theory->deleteLanguage($lang['lcid']);

                $intro = new Intro();
                $intro->deleteLanguage($lang['lcid']);

                Language::where('id', $id)->delete();

                return response('success', 200);
            }
        }
        return response('error', 401);
    }

    public function mainMenuDestroy($id)
    {
        if (\Auth::check() && \Auth::user()->hasRole(['admin','qa','project','developer'])) {
            $lang = Language::where('id', $id)->firstOrFail();

            if ($lang['lcid'] == "en") {
                return response('fail', 406);
            } else {
                Language::where('id', $id)->update([
                    'deleted' => 0,
                    'is_displayed_to_user' => 0,
                ]);
                return response('success', 200);
            }
        }
        return response('error', 401);
    }
}
