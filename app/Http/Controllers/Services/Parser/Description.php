<?php

namespace App\Http\Controllers\Services\Parser;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\Text;
use App\Models\TheoryPage;
use DOMDocument;
use Illuminate\Http\Request;
use Symfony\Component\DomCrawler\Crawler;

class Description extends Controller
{
    public function descriptionDataGet($id, $lcid, $key, $theoryPageArray)
    {
        if (array_key_exists($id, $theoryPageArray)) {
            $theoryPage = $theoryPageArray[$id];
        } else {
            $theoryPage = $theoryPageArray[$id] = TheoryPage::find($id);
        }

        return [$this->theoryPageGet($theoryPage, $lcid, $key) ?? [],$theoryPageArray];
    }

    public function theoryPageGet($theoryPage, $lcid, $inckey)
    {
        $textFunctions = new Text();
        $doc = new DOMDocument();
        $arrayOfSentences = [];
        if (isset($theoryPage)) {
            $html = json_decode($textFunctions->onlyLocalisationGet($lcid, 'text', $theoryPage));
            if (isset($html)) {
                foreach ($html as $inc => $item) {
                    $doc->loadHTML($item);
                    foreach ($doc->getElementsByTagName('p') as $value) {
                        $arrayOfSentences[] = [
                            "id" => 14 * $inckey + $inc,
                            "items" => $this->hasChild($value),
                        ];
                    }
                }
            }
        }
        return $arrayOfSentences;
    }

    public function hasChild($p)
    {
        $arrayOfSentences = [];
//        $listenable = 0;
        $res = [];
        if ($p->hasChildNodes()) {
            foreach ($p->childNodes as $values) {
                $value = utf8_decode($values->nodeValue);
                $color = '#333452';
                $cursive = 0;
                $bold = 0;
                $listenable = 0;
                $underline = 0;
                if ($values->hasChildNodes()) {
                    switch ($values->tagName) {
                        case('strong'):
                            $bold = 1;
                            break;
                        case('u'):
                            $underline = 1;
                            break;

                        case('i'):
                            $cursive = 1;
                            break;

                        case('span'):
                            $temp = null;
                            if ($values->getAttributeNode('style')) {
                                $temp = str_replace('color:', '', $values->getAttributeNode('style')->value);
                            }
                            $color = $temp;
                            break;

                        case('s'):
                            $listenable = 1;
                            break;
                        default:
                            break;
                    }
                    [$values, $value, $color, $cursive, $bold, $listenable, $underline] = $this->recursiveTagDataGet($values, $value, $color, $cursive, $bold, $listenable, $underline);
                }
                if ($values->hasChildNodes()) {
//                    dump(utf8_decode($values->nodeValue));
                    foreach ($values->childNodes as $c) {
                        $value = utf8_decode($c->nodeValue);

                        if ($c->hasChildNodes()) {
                            switch ($c->tagName) {
                                case('strong'):
                                    $bold = 1;
                                    break;
                                case('u'):
                                    $underline = 1;
                                    break;

                                case('i'):
                                    $cursive = 1;
                                    break;

                                case('span'):
                                    $temp = null;
                                    if ($c->getAttributeNode('style')) {
                                        $temp = str_replace('color:', '', $c->getAttributeNode('style')->value);
                                    }
                                    $color = $temp;
                                    break;

                                case('s'):
                                    $listenable = 1;
                                    break;
                                default:
                                    break;
                            }
                            [$c, $value, $color, $cursive, $bold, $listenable, $underline] = $this->recursiveTagDataGet($c, $value, $color, $cursive, $bold, $listenable, $underline);
                            $arrayOfSentences[] = [
                                'value' => $value,
                                'color' => $color,
                                'cursive' => $cursive,
                                'bold' => $bold,
                                'listenable' => $listenable,
                                'underline' => $underline
                            ];
                        } else {

                            if (isset($c->tagName)) {
                                switch ($c->tagName) {
                                    case('strong'):
                                        $bold = 1;
                                        break;
                                    case('u'):
                                        $underline = 1;
                                        break;

                                    case('i'):
                                        $cursive = 1;
                                        break;

                                    case('span'):
                                        $temp = null;
                                        if ($c->getAttributeNode('style')) {
                                            $temp = str_replace('color:', '', $c->getAttributeNode('style')->value);
                                        }
                                        $color = $temp;
                                        break;

                                    case('s'):
                                        $listenable = 1;
                                        break;
                                    default:
                                        break;
                                }
                                $arrayOfSentences[] = [
                                    'value' => $value,
                                    'color' => $color,
                                    'cursive' => $cursive,
                                    'bold' => $bold,
                                    'listenable' => $listenable,
                                    'underline' => $underline
                                ];
                            } else {
                                $arrayOfSentences[] = [
                                    'value' => utf8_decode($c->nodeValue),
                                    'color' => $color,
                                    'cursive' => $cursive,
                                    'bold' => $bold,
                                    'listenable' => $listenable,
                                    'underline' => $underline
                                ];
                            }
                        }
                    }
                } else {
                    $value = utf8_decode($values->nodeValue);
                    $color = '#333452';
                    $cursive = 0;
                    $bold = 0;
                    $listenable = 0;
                    $underline = 0;
                    if ($values->hasChildNodes()) {
                        switch ($values->tagName) {
                            case('strong'):
                                $bold = 1;
                                break;
                            case('u'):
                                $underline = 1;
                                break;

                            case('i'):
                                $cursive = 1;
                                break;

                            case('span'):
                                $temp = null;
                                if ($values->getAttributeNode('style')) {
                                    $temp = str_replace('color:', '', $values->getAttributeNode('style')->value);
                                }
                                $color = $temp;
                                break;

                            case('s'):
                                $listenable = 1;
                                break;
                            default:
                                break;
                        }
                        [$values, $value, $color, $cursive, $bold, $listenable, $underline] = $this->recursiveTagDataGet($values, $value, $color, $cursive, $bold, $listenable, $underline);
                        $arrayOfSentences[] = [
                            'value' => $value,
                            'color' => $color,
                            'cursive' => $cursive,
                            'bold' => $bold,
                            'listenable' => $listenable,
                            'underline' => $underline
                        ];
                    } else {
                        if (isset($values->tagName)) {
                            switch ($values->tagName) {
                                case('strong'):
                                    $bold = 1;
                                    break;
                                case('u'):
                                    $underline = 1;
                                    break;

                                case('i'):
                                    $cursive = 1;
                                    break;

                                case('span'):
                                    $temp = null;
                                    if ($values->getAttributeNode('style')) {
                                        $temp = str_replace('color:', '', $values->getAttributeNode('style')->value);
                                    }
                                    $color = $temp;
                                    break;

                                case('s'):
                                    $listenable = 1;
                                    break;
                                default:
                                    break;
                            }
                            $arrayOfSentences[] = [
                                'value' => $value,
                                'color' => $color,
                                'cursive' => $cursive,
                                'bold' => $bold,
                                'listenable' => $listenable,
                                'underline' => $underline
                            ];
                        } else {
                            $arrayOfSentences[] = [
                                'value' => utf8_decode($values->nodeValue),
                                'color' => '#333452',
                                'cursive' => 0,
                                'bold' => 0,
                                'listenable' => 0,
                                'underline' => 0
                            ];
//                    $array[] = [
//                        'id' => $key,
//                        'sentenceWords' => [
//                            'value' => utf8_decode($c->nodeValue),
//                            'color' => null,
//                            'cursive' => 0,
//                            'bold' => 0
//                        ],
//                        "listenable" => 0
//                    ];
                        }

                    }
                }
            }
        }
        $listenableArrayOfSentence = [];
        $junctionIndicator = 0;
        $lastone = 0;
        $lastForKey = 0;
        foreach ($arrayOfSentences as $key => $items) {
            $lastForKey = $key;
            if ($items['listenable'] == $junctionIndicator) {
                $listenableArrayOfSentence[] = $items;
                $lastone = 1;
            } else {
                if (isset($listenableArrayOfSentence) && !empty($listenableArrayOfSentence)) {
                    $res[] = [
                        'id' => $key,
                        'sentenceWords' => $listenableArrayOfSentence,
                        "listenable" => $junctionIndicator
                    ];

                }
                if (count($arrayOfSentences) == 1) {
                    $lastone = 1;
                }
                $listenableArrayOfSentence = [];
                $junctionIndicator = $items['listenable'];
                if (count($arrayOfSentences) == $key + 1) {
                    $lastone = 1;
                }
                $listenableArrayOfSentence[] = $items;
            }
        }
        if ($lastone == 1) {
            if (isset($listenableArrayOfSentence) && !empty($listenableArrayOfSentence)) {
                $res[] = [
                    'id' => $lastForKey,
                    'sentenceWords' => $listenableArrayOfSentence,
                    "listenable" => $junctionIndicator
                ];
            }
        }

        return $res;
    }

    public function recursiveTagDataGet($p, $value, $color, $cursive, $bold, $listenable, $underline)
    {
        foreach ($p->childNodes as $c) {
            if ($c->hasChildNodes()) {
                if (isset($c->tagName)) {
                    switch ($c->tagName) {
                        case('strong'):
                            $bold = 1;
                            break;
                        case('u'):
                            $underline = 1;
                            break;

                        case('i'):
                            $cursive = 1;
                            break;

                        case('span'):
                            $temp = null;
                            if ($c->getAttributeNode('style')) {
                                $temp = str_replace('color:', '', $c->getAttributeNode('style')->value);
                            }
                            $color = str_replace(";", "", $temp ?? $color);
                            break;
                        case('s'):
                            $listenable = 1;
                            break;
                        default:
                            break;
                    }
                }
                [$c, $value, $color, $cursive, $bold, $listenable, $underline] = $this->recursiveTagDataGet($c, $value, $color, $cursive, $bold, $listenable, $underline);
            }
        }
        return [$p, $value, $color, $cursive, $bold, $listenable, $underline];
    }
}
