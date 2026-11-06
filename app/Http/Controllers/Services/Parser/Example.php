<?php

namespace App\Http\Controllers\Services\Parser;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\Text;
use App\Models\TheoryPage;
use DOMDocument;
use Illuminate\Http\Request;

class Example extends Controller
{
    public function exampleDataGet($id, $lcid, $inckey,$theoryPagesArray = [])
    {
        if (array_key_exists($id,$theoryPagesArray)){
            $theoryPage = $theoryPagesArray[$id];
        }else{
            $theoryPage = $theoryPagesArray[$id] = TheoryPage::find($id);
        }
        $textFunctions = new Text();
        $doc = new DOMDocument();
        $arrayOfSentences = [];
        $descriptionFunctions = new Description();
        if (isset($theoryPage)) {
            $html = json_decode($textFunctions->onlyLocalisationGet($lcid, 'text', $theoryPage));
            if (isset($html)) {
                foreach ($html as $inc => $item) {
                    $levelArray = [];
                    $doc->loadHTML($item);
                    foreach ($doc->getElementsByTagName('p') as $key => $value) {
                        $levelArray[] = [
                            "id" => $key,
                            "items" => $descriptionFunctions->hasChild($value),
                        ];
                    }
                    $arrayOfSentences[] = [
                        "id" => 24 * $inckey + $inc,
                        "items" => $levelArray,
                    ];
                }
            }
        }

        return [$arrayOfSentences,$theoryPagesArray];
    }
}
