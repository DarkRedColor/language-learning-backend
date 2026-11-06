<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\Parser\Description;
use App\Http\Controllers\Services\Parser\Example;
use App\Http\Controllers\Services\Parser\Image;
use App\Http\Controllers\Services\Parser\Table;
use App\Http\Controllers\Services\Parser\Title;
use App\Models\TheoryPage;
use App\Models\TheoryPageName;
use DOMDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Theory extends Controller
{
    private $theoryPagesArray = [];

    private function wordLocalisationGet($item, $lcid)
    {
        $res = $item->word;
        $localisedField = "meaning_$lcid";
        if (isset($item->$localisedField)) {
            $res = $item->$localisedField;
        }
        return $res;
    }

    private $typeArray = [
        0 => 0,
        1 => 2,
        2 => 1
    ];

    public function buildTheoryData($theories, $size, $lcid, $v2 = false)
    {
        $res = [];

        foreach ($theories as $theory) {
            $theoryData = [
                'id' => $theory->id,
                'title' => null,
                'type' => null,
                'contentOrder' => null,
                'icons' => null,
                'sentences' => null,
                'examples' => null,
            ];
            $wordTheoryData = [
                'id' => null,
                'text' => null,
                'translated_text' => null,
                'transcription' => null,
                'example' => null
            ];
            if ($v2) {
                $wordTheoryData['image'] = null;
            } else {
                $wordTheoryData['imageURL'] = null;
            }
            $theory = \App\Models\Theory::with('words')->find($theory->id);
            $type = $theory->type;
            $theoryPages = null;
            if ($type <> 0)
                $theoryPages = TheoryPageName::with('pages')->where('theory_id', $theory->id)->get();
            $titleFunctions = new Title();
            $descriptionFunctions = new Description();
            $exampleFunctions = new Example();
            $tableFunctions = new Table();
            $wordTheoryDataArray = null;
            $framesArray = null;

            $pagesArray = null;
            $doc = new DOMDocument();
            $imagesFunctions = new Images();
            if (isset($theory)) {
                $theoryData['type'] = $theory->type;
                if ($theoryData['type'] == 0) {
                    if (isset($theory->words))
                        foreach ($theory->words as $item) {
                            $array = null;
                            $itemsArray = null;
                            $framesArray[] = [
                                'frameId' => $item->id,
                                'type' => 0
                            ];
                            $wordTheoryData['id'] = $item->id;
                            $img = null;
                            if (isset($item->image)) {
                                $img = Storage::disk('gcs')->url("theory/$item->id" . "_$size.webp");
                            }

                            $timeStamps = $item->updated_at->getTimestamp();
                            $mediaUrl = $img;
                            if ($v2) {
                                $wordTheoryData['image'] = $imagesFunctions->apiMediaModel($mediaUrl, $timeStamps, 0);
                            } else {
                                $wordTheoryData['imageURL'] = $mediaUrl;
                            }
                            $wordTheoryData['text'] = $item->word;
                            $wordTheoryData['translated_text'] = $this->wordLocalisationGet($item, $lcid);
                            $wordTheoryData['transcription'] = $item->transcription;
                            $html = $item->example_list;
                            if (isset($item->example_list) && !is_array($item->example_list))
                                $html = json_decode($item->example_list);
                            if (isset($html)) {
                                foreach ($html as $key => $entity) {
                                    $doc->loadHTML($entity);
                                    foreach ($doc->getElementsByTagName('p') as $value) {
                                        $itemsArray [] = $descriptionFunctions->hasChild($value);
                                    }
                                    $array = [
                                        "id" => $key,
                                        "items" => $itemsArray[0] ?? null,
                                    ];
                                }
                                $wordTheoryData['example'] = $array;
                            }
                            if ($v2) {
                                $wordTheoryData['audio'] = null;
                                $wordTheoryData['audio_spelling'] = null;

                            } else {
                                $wordTheoryData['audio_url'] = null;
                                $wordTheoryData['audio_spelling_url'] = null;

                            }
                            $hostCheck = new HostChecker();
                            $host = $hostCheck->hostCheck(config('values.host'));

                            if ($item->by_word == 1) {
                                $timeStamps = $item->updated_at->getTimestamp();
                                $mediaUrl = Storage::disk('gcs')->url("word/audio/" . str_replace(' ', '_', $item->word) . '_word.mp3');
                                if ($v2) {
                                    $wordTheoryData['audio'] = $imagesFunctions->apiMediaModel($mediaUrl, $timeStamps, 1);
                                } else {
                                    $wordTheoryData['audio_url'] = $mediaUrl;
                                }
                            }
                            if ($item->by_letter == 1) {
                                $timeStamps = $item->updated_at->getTimestamp();
                                $mediaUrl = Storage::disk('gcs')->url("word/audio/" . str_replace(' ', '_', $item->word) . '_letter.mp3');
                                if ($v2) {
                                    $wordTheoryData['audio_spelling'] = $imagesFunctions->apiMediaModel($mediaUrl, $timeStamps, 1);
                                } else {
                                    $wordTheoryData['audio_spelling_url'] = $mediaUrl;
                                }
                            }

                            $wordTheoryDataArray [] = $wordTheoryData;
                        }
                    $theoryData = $wordTheoryDataArray;
                } else {
                    foreach ($theoryPages as $pages) {
                        if (isset($pages->pages)) {
                            $contentOrder = null;
                            $arrayOfDescriptionTheoriesData = null;
                            $arrayOfExamplesTheoriesData = null;
                            $arrayOfTablesTheoriesData = null;
                            $framesArray[] = [
                                'frameId' => $pages->id,
                                'type' => 1
                            ];
                            $contentOrderDataArray = null;

                            foreach ($pages->pages->SortBy('order') as $key => $item) {
                                if ($item->type <> 0)
                                    $contentOrderDataArray = [
                                        'type' => null,
                                        'dataId' => null
                                    ];
                                $contentOrderArray = null;
                                switch ($item->type) {
                                    case (0):
                                        $theoryData['title'] = $titleFunctions->theoryTitleDataGet($item->id, $lcid);
                                        break;
                                    case (1):
//                                        $contentOrderDataArray['type'] = 1;
//                                        $contentOrderDataArray['dataId'] = $key;
                                        if (array_key_exists($item->id, $this->theoryPagesArray)) {
                                            $theoryPage = $this->theoryPagesArray[$item->id];
                                        } else {
                                            $theoryPage = $this->theoryPagesArray[$item->id] = TheoryPage::find($item->id);
                                        }

                                        if (isset($theoryPage)) {
                                            $localisedField = "text_$lcid";
                                            $data = json_decode($theoryPage->text_en);
                                            if (json_decode($theoryPage->$localisedField) != null) {
                                                $data = json_decode($theoryPage->$localisedField);
                                            }
                                            if ($data != null) {
                                                foreach ($data as $inc => $value) {
                                                    $contentOrderDataArray['type'] = 1;
                                                    $contentOrderDataArray['dataId'] = 14 * $key + $inc;
                                                    $contentOrderArray [] = $contentOrderDataArray;
                                                }
                                            }
                                        }
                                        $contentOrderDataArray = null;
                                        [$arrayOfDescriptionTheoriesData [], $this->theoryPagesArray] = $descriptionFunctions->descriptionDataGet($item->id, $lcid, $key, $this->theoryPagesArray);
//                                    $theoryData['sentences'] = $descriptionFunctions->descriptionDataGet($item->id, $lcid);
                                        break;
                                    case (2):

                                        if (array_key_exists($item->id, $this->theoryPagesArray)) {
                                            $theoryPage = $this->theoryPagesArray[$item->id];
                                        } else {
                                            $theoryPage = $this->theoryPagesArray[$item->id] = TheoryPage::find($item->id);
                                        }

                                        if (isset($theoryPage)) {
                                            $localisedField = "text_$lcid";
                                            $data = json_decode($theoryPage->text_en);
                                            if (json_decode($theoryPage->$localisedField) != null) {
                                                $data = json_decode($theoryPage->$localisedField);
                                            }
                                            if ($data != null) {
                                                foreach ($data as $inc => $value) {
                                                    $contentOrderDataArray['type'] = 2;
                                                    $contentOrderDataArray['dataId'] = 24 * $key + $inc;
                                                    $contentOrderArray [] = $contentOrderDataArray;
                                                }
                                            }
                                        }
                                        $contentOrderDataArray = null;
                                        [$arrayOfExamplesTheoriesData[], $this->theoryPagesArray] = $exampleFunctions->exampleDataGet($item->id, $lcid, $key, $this->theoryPagesArray);


//                                    $theoryData['examples'] = $exampleFunctions->exampleDataGet($item->id, $lcid);
                                        break;
                                    case (3):
                                        $contentOrderDataArray['type'] = 0;
                                        $contentOrderDataArray['dataId'] = 34 * $key;
                                        $imageFunctions = new Image();
                                        $timeStamps = $item->updated_at->getTimestamp();
                                        $mediaUrl = $imageFunctions->imageDataGet($item->id, $size);
                                        if ($v2) {
                                            $theoryData['icons'] = [
                                                [
                                                    'id' => 34 * $key,
                                                    'image' => $imagesFunctions->apiMediaModel($mediaUrl, $timeStamps, 0),
                                                ]
                                            ];
                                        } else {
                                            $theoryData['icons'] = [
                                                [
                                                    'id' => 34 * $key,
                                                    'image' => $mediaUrl,
                                                ]
                                            ];
                                        }
                                        break;
                                    case (4):
                                        $contentOrderDataArray['type'] = 3;
                                        $contentOrderDataArray['dataId'] = 44 * $key;
                                        $arrayOfTablesTheoriesData[] = $tableFunctions->tableDataGet($item->id, $lcid, 44 * $key);
//                                    $theoryData['tables'] = $tableFunctions->tableDataGet($item->id, $lcid);
                                        break;
                                    default:
                                        break;
                                }
                                if ($contentOrderDataArray <> null)
                                    $contentOrder[] = $contentOrderDataArray;
                                if (isset($contentOrderArray)) {
                                    foreach ($contentOrderArray as $value)
                                        $contentOrder[] = $value;
                                }
                            }

                            $theoryData['sentences'] = $this->deleteExcesiveLevelOfArray($arrayOfDescriptionTheoriesData);
                            $theoryData['examples'] = $this->deleteExcesiveLevelOfArray($arrayOfExamplesTheoriesData);
                            $theoryData['tables'] = $this->deleteExcesiveLevelOfArray($arrayOfTablesTheoriesData);
                            $theoryData['contentOrder'] = $contentOrder;
                            $theoryData['id'] = $pages->id;

                        }
                        $pagesArray[] = $theoryData;
                    }
                }
            }
            $buildedTheoryData = [
                'theory_id' => $theory->id,
                'tags' => $theory->tags,
                'type' => $this->typeArray[$type],
                'frames' => $framesArray,
                'frameWordData' => [],
                'frameOrderedData' => [],
            ];

            if ($type == 0) {
                $buildedTheoryData['frameWordData'] = $theoryData;
                $buildedTheoryData['theory_title'] = null;

            } else {
                $buildedTheoryData['frameOrderedData'] = $pagesArray;
                $buildedTheoryData['theory_title'] = $pagesArray[0]['title'] ?? null;


            }
            $res [] = $buildedTheoryData;
        }


        return $res;
    }

    private function deleteExcesiveLevelOfArray($array)
    {
        $res = null;
        if (isset($array))
            foreach ($array as $item) {
                if (is_array($item)) {
                    foreach ($item as $value) {
                        $res [] = $value ?? null;
                    }
                }

            }
        return $res;
    }
}
