<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Services\ApiRequest;
use App\Http\Controllers\Services\HostChecker;
use App\Models\Intro;
use App\Models\Language;
use App\Models\Theory;
use App\Models\Word;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Route;

class WordController extends Controller
{
    public function getContent($name)
    {
        $apiRequest = new ApiRequest();
        return $apiRequest->getSftpContent($name, 'word/audio', 'word/audio');
    }

    public function getImgContent($name)
    {
        $apiRequest = new ApiRequest();
        return $apiRequest->getSftpContent($name, 'word/img', 'word/img');
    }

    private function xmlToArray(\SimpleXMLElement $xml): array
    {
        $parser = function (\SimpleXMLElement $xml, array $collection = []) use (&$parser) {
            $nodes = $xml->children();
            $attributes = $xml->attributes();

            if (0 !== count($attributes)) {
                foreach ($attributes as $attrName => $attrValue) {
                    $collection['attributes'][$attrName] = strval($attrValue);
                }
            }

            if (0 === $nodes->count()) {
                $collection['value'] = strval($xml);
                return $collection;
            }

            foreach ($nodes as $nodeName => $nodeValue) {
                if (count($nodeValue->xpath('../' . $nodeName)) < 2) {
                    $collection[$nodeName] = $parser($nodeValue);
                    continue;
                }

                $collection[$nodeName][] = $parser($nodeValue);
            }

            return $collection;
        };

        return [
            $xml->getName() => $parser($xml)
        ];
    }

    private function parseDefinitionsAndExamples($xmlString)
    {
        $xml = new \SimpleXMLElement($xmlString);
        if (!isset($xml->{'sn-gs'}) || !isset($xml->{'sn-gs'}->{'sn-g'})) {
            return null;
        }
        $senses = $this->xmlToArray($xml->{'sn-gs'}->{'sn-g'});
        $result = [];
        $data1 = [];

        if (isset($senses['sn-g']['sense-topg']['def']['value'])) {
            $data1['definition'] = $senses['sn-g']['sense-topg']['def']['value'];
        } else if (isset($senses['sn-g']['sense-topg']['def']['Ref']['value'])) {
            $data1['definition'] = $senses['sn-g']['sense-topg']['def']['Ref']['value'];
        } else if (isset($senses['sn-g']['sense-topg']['def']['Ref']['span']['value'])) {
            $data1['definition'] = $senses['sn-g']['sense-topg']['def']['Ref']['span']['value'];
        } else if (isset($senses['sn-g']['def']['value'])) {
            $data1['definition'] = $senses['sn-g']['def']['value'];
        } else if (isset($senses['sn-g']['sense-topg']['def']['Ref'][0]['span']['value'])) {
            $data1['definition'] = $senses['sn-g']['sense-topg']['def']['Ref'][0]['span']['value'];
        } else if (isset($senses['sn-g']['def']['Ref'][0]['span']['value'])) {
            //$data1['definition'] = $senses['sn-g']['def']['Ref'][0]['span']['value'];
            $data1['definition'] = join(' / ', array_map(function ($def) {
                return $def['span']['value'];
            }, $senses['sn-g']['def']['Ref']));
        } else if (isset($senses['sn-g']['def']['Ref']['span']['value'])) {
            $data1['definition'] = $senses['sn-g']['def']['Ref']['span']['value'];
        } else if (isset($senses['sn-g']['def']['span']['value'])) {
            $data1['definition'] = $senses['sn-g']['def']['span']['value'];
        } else if (isset($senses['sn-g']['sense-topg']['def']['dh']['value'])) {
            $data1['definition'] = $senses['sn-g']['sense-topg']['def']['dh']['value'];
        } else if (isset($senses['sn-g']['sense-topg']['def']['ebi']['value'])) {
            $data1['definition'] = $senses['sn-g']['sense-topg']['def']['ebi']['value'];
        } else if (isset($senses['sn-g']['sense-topg']['def']['span']['value'])) {
            $data1['definition'] = $senses['sn-g']['sense-topg']['def']['span']['value'];
        } else if (isset($senses['sn-g']['def']['dh']['value'])) {
            $data1['definition'] = $senses['sn-g']['def']['dh']['value'];
        } else if (isset($senses['sn-g']['sense-topg']['def']['gl']['Ref']['span']['value'])) {
            $data1['definition'] = $senses['sn-g']['sense-topg']['def']['gl']['Ref']['span']['value'];
        } else if (isset($senses['sn-g']['sense-topg']['def']['def_qt'][0]['span'][0])) {
            $data1['definition'] = join(' ', array_map(function ($v) {
                return $v['value'];
            }, $senses['sn-g']['sense-topg']['def']['def_qt'][0]['span']));
        }

        if (isset($senses['sn-g']['x-gs']['x-g']['x']['cl']['value'])) {
            $data1['examples'][] = $senses['sn-g']['x-gs']['x-g']['x']['cl']['value'];
        } else if (isset($senses['sn-g']['x-gs']['x-g']['x']['value'])) {
            $data1['examples'][] = $senses['sn-g']['x-gs']['x-g']['x']['value'];
        } else if (isset($senses['sn-g']['x-gs']['x-g']['x']['gl']['value'])) {
            $data1['examples'][] = $senses['sn-g']['x-gs']['x-g']['x']['gl']['value'];
        } else {
            if (isset($senses['sn-g']['x-gs']['x-g'])) {
                foreach ($senses['sn-g']['x-gs']['x-g'] as $xg) {
                    if (isset($xg['x']['value']) || isset($xg['x']['gl']['value'])) {
                        $data1['examples'][] = $xg['x']['value'] ?? $xg['x']['gl']['value'];
                    } else if (isset($xg['x']['cl'])) {
                        $data1['examples'] = array_map(function ($example) {
                            return is_array($example) ? $example['value'] : $example;
                        }, $xg['x']['cl']);
                    }

                }
            }
        }
        /*
        if (!isset($data1['definition'])) {
            dd($senses);
        }
        */
        if (empty($data1) || !isset($data1['examples'])) {
            return null;
        }
        $result[] = $data1;

        return $result;
    }

    private function parsePhoneticSpelling($xmlString)
    {
        $xml = new \SimpleXMLElement($xmlString);
        return isset($xml->{'top-g'}->phonetics->{'pron-gs'}) ? (string)$xml->{'top-g'}->phonetics->{'pron-gs'}->phon : null;
    }

    public function getDataFromOxford(Request $request)
    {
        $response = Http::withHeaders([
            'accessKey' => config('values.oxford_api_key'),
            'Content-Type' => 'application/json'
        ])->get(config('values.oxford_base_url') . '/dictionaries/english/search?q=' . $request['word']);

        if (!$response->successful()) {
            return response('Error while retrieving entryIds', $response->status());
        }

        $response = json_decode($response->body(), true);
        $defAndExam = [];
        $phoneticSpelling = [];
        foreach ($response['results'] as $entry) {
            $responseEntry = Http::withHeaders([
                'accessKey' => config('values.oxford_api_key'),
                'Content-Type' => 'application/json'
            ])->get(config('values.oxford_base_url') . '/dictionaries/english/entries/' . $entry['entryId'] . '?format=xml');
            if (!$responseEntry->successful()) {
                continue;
            }
            $responseEntry = json_decode($responseEntry->body(), true);
            $phoneticSpelling[] = $this->parsePhoneticSpelling($responseEntry['entryContent']);
            $parsedDefAndExam = $this->parseDefinitionsAndExamples($responseEntry['entryContent']);
            if (!empty($parsedDefAndExam)) {
                $defAndExam[] = $parsedDefAndExam;
            }
            if (count($defAndExam) >= 3) {
                break;
            }

        }

        $oxfordApiRes = count($response['results']) ? ['phoneticSpelling' => !empty($phoneticSpelling) ? $phoneticSpelling[0] : '', 'defAndExam' => $defAndExam, 'localisation' => [], 'word' => $request->word] : null;

        return view('admin.words.includes.oxford-response')->with(['oxfordApiRes' => $oxfordApiRes])->render();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index($filter = 'word', $order = 'asc')
    {
        $words = Word::orderBy($filter, $order)->get();

        $languages = Language::where('deleted', 1)->get();

        return view('admin.words.index')->with(compact('words', 'languages', 'order', 'filter'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return string
     */

    public function create()
    {
        $languages = Language::where('deleted', 1)->get();

        return view('admin.words.includes.create')->with(compact('languages'))->render();
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
            'word' => 'required',
        ]);

        $keyData = [];
        [$keys, $values] = Arr::divide($request->all());
        for ($i = 3; $i < count($keys); $i++) {
            $keyData [$keys[$i]] = $values[$i];
        }
        $keyData['image'] = null;
        if (!empty($request->example_list)) {
            $keyData['example_list'] = json_decode($request->example_list);
        }
        $apiRequest = new ApiRequest();
        if ($request->hasFile('audio-word')) {
            $audio = $request->file('audio-word');
            $nameWord = str_replace(' ', '_', $request->word . '_word.mp3');
            $apiRequest->postSftpContent($audio, $nameWord, 'word/audio');
            $keyData['by_word'] = 1;

        }
        if ($request->hasFile('audio-letter')) {
            $audio = $request->file('audio-letter');
            $nameLetter = str_replace(' ', '_', $request->word . '_letter.mp3');
            $apiRequest->postSftpContent($audio, $nameLetter, 'word/audio');
            $keyData['by_letter'] = 1;
        }
        unset($keyData['audio-word']);
        unset($keyData['audio-letter']);
//        dd($keyData);

        Word::create($keyData);

        $word = Word::orderBy('created_at', 'desc')->first();
        $id = $word->id;
        if ($request->dd_file <> null) {
            $apiRequest = new ApiRequest();
            $apiRequest->imagesRequestDataFill($id, $request, 'word', 'image');


            Word::where('id', $id)->update([
                'image' => config('values.api_url') . 'word/' . $id,
            ]);

        }

        return redirect(route('word-index'))->with(compact('id'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return string
     */
    public function edit($id)
    {
        $word = Word::find($id);
        $languages = Language::where('deleted', 1)->get();
        $hostCheck = new HostChecker();
        $host = $hostCheck->hostCheck(config('values.host'));

        return view('admin.words.includes.edit')->with(compact('word', 'languages', 'host'))->render();
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
        [$keys, $values] = Arr::divide($request->all());
        $keyData = [];
        for ($i = 3; $i < count($keys); $i++) {
            $keyData [$keys[$i]] = $values[$i];
        }
        unset($keyData['image']);

        if (!empty($request->example_list)) {
            $keyData['example_list'] = json_decode($request->example_list);
        }
        $apiRequest = new ApiRequest();
        $disk = 'word/audio';
        if ($request['update-audio-word'] == 'true') {
            if ($request->hasFile('audio-word')) {
                $audio = $request->file('audio-word');
                $nameWord = str_replace(' ', '_', $request->word . '_word.mp3');
                if (Storage::disk($disk)->exists("$nameWord")) {
                    Storage::disk($disk)->delete("$nameWord");
                }
                $apiRequest->postSftpContent($audio, $nameWord, 'word/audio');
                $keyData['by_word'] = 1;
            } else {
                $keyData['by_word'] = 0;
                $nameWord = str_replace(' ', '_', $request->word . '_word.mp3');
                if (Storage::disk('gcs')->exists('/word/audio/' . $nameWord)) {
                    Storage::disk('gcs')->delete('/word/audio/' . $nameWord);
                }
                if (Storage::disk($disk)->exists("$nameWord")) {
                    Storage::disk($disk)->delete("$nameWord");
                }

            }
        }
        if ($request['update-audio-letter'] == 'true') {
            if ($request->hasFile('audio-letter')) {
                $audio = $request->file('audio-letter');
                $nameLetter = str_replace(' ', '_', $request->word . '_letter.mp3');
                if (Storage::disk($disk)->exists("$nameLetter")) {
                    Storage::disk($disk)->delete("$nameLetter");
                }
                $apiRequest->postSftpContent($audio, $nameLetter, 'word/audio');
                $keyData['by_letter'] = 1;
            } else {
                $keyData['by_letter'] = 0;
                $nameWord = str_replace(' ', '_', $request->word . '_letter.mp3');
                if (Storage::disk('gcs')->exists('/word/audio/' . $nameWord)) {
                    Storage::disk('gcs')->delete('/word/audio/' . $nameWord);
                }
                if (Storage::disk($disk)->exists("$nameWord")) {
                    Storage::disk($disk)->delete("$nameWord");
                }
            }
        }

        Word::find($id)->update($keyData);

        $deleted = $request->is_deleted;
        $file = $request->dd_file;
        if ($file <> null && $deleted <> null || $file <> null && $deleted == null) {
            $apiRequest = new ApiRequest();
            $apiRequest->imagesRequestDataFill($id, $request, 'word', 'image');


            Word::find($id)->update([
                'image' => config('values.api_url') . 'word/' . $id,
            ]);

        } elseif ($deleted <> null && $file == null) {
            if (Storage::disk('gcs')->exists('/word/' . $id)) {
                Storage::disk('gcs')->delete('/word/' . $id);


                Word::find($id)->update([
                    'image' => null,
                ]);

            }

            if (isset($request->theoryId)) {
                return redirect(route('theory-words-index', $request->theoryId));
            } else {
                return redirect(route('word-index'))->with(compact('id'));

            }
        }
    }
        /**
         * Remove the specified resource from storage.
         *
         * @param int $id
         * @return \Illuminate\Http\Response
         */
        public
        function destroy($id)
        {
            if (\Auth::check() && \Auth::user()->hasRole(['admin', 'qa', 'project', 'developer', 'methodologist'])) {

//            --------------------- For features model links -----------------

//            $arrOfModels = [ new Theory()];
//            foreach ($arrOfModels as $model) {
//                $model->find($id)->update([
//                    'word_id' => null
//                ]);
//            }

                $word = Word::find($id);
                $nameWord = str_replace(' ', '_', $word->word . '_letter.mp3');
                if (Storage::disk('gcs')->exists( 'word/audio/' . $nameWord)) {
                    Storage::disk('gcs')->delete( 'word/audio/' . $nameWord);
                }
                $nameWord = str_replace(' ', '_', $word->word . '_word.mp3');
                if (Storage::disk('gcs')->exists( 'word/audio/' . $nameWord)) {
                    Storage::disk('gcs')->delete( 'word/audio/' . $nameWord);
                }
                $word->delete();

                return response('success', 200);
            }
            return response('error', 401);
        }
    }
