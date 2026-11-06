<?php

namespace App\Http\Controllers\Services\Parser;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\Text;
use App\Models\TheoryPage;
use DOMDocument;
use Illuminate\Http\Request;

class Table extends Controller
{
    public function tableDataGet($id, $lcid, $key)
    {
        $theoryPage = TheoryPage::find($id);
        return $this->tableDataBuild($theoryPage, $lcid, $key);
    }

    public function tableDataBuild($theoryPage, $lcid, $key)
    {
        $textFunctions = new Text();
        $doc = new DOMDocument();
        $arrayOfSentences = [];
        if (isset($theoryPage)) {
            $html = json_decode($textFunctions->onlyLocalisationGet($lcid, 'text', $theoryPage));
            if (isset($html)) {
                foreach ($html as $item) {
                    $doc->loadHTML($item);
                    foreach ($doc->getElementsByTagName('tbody') as $value) {
                        $arrayOfSentences[] = [
                            "id" => $key,
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
        $array = [];
        if ($p->hasChildNodes()) {
            foreach ($p->childNodes as $c) {
                if (isset($c->tagName)) {
                    if ($c->tagName == 'tr') {
                        if ($c->hasChildNodes()) {
                            $subArray = [];
                            foreach ($c->childNodes as $key => $li) {
                                $spanDelete = $this->spanDelete($li);
                                if (count($spanDelete) <> 0) {
                                    $subArray[] = [
                                        'id' => $key,
                                        'items' => $spanDelete
                                    ];
                                } else {
                                    $subArray[] = [
                                        'id' => $key,
                                        'items' => null
                                    ];

                                }
                            }
                            $array [] = $subArray;
                        }
                    }
                }
            }
        }
        return $array;
    }

    public function spanDelete($p)
    {
        $descriptionFunctions = new Description();
        $array = [];
        $subArray = [];
        $color = '#333452';
        if ($p->hasChildNodes()) {
            foreach ($p->childNodes as  $c) {
                $value = utf8_decode($c->nodeValue);
                $cursive = 0;
                $bold = 0;
                $listenable = 0;
                $underline = 0;
                $key = 0;
                if ($value <> "") {
                    if ($c->hasChildNodes()) {
                        foreach ($c->childNodes as $key =>$intoSpan) {
                            if (isset($intoSpan->tagName)) {
                                $subArray = [];
                                $value = utf8_decode($intoSpan->nodeValue);
                                switch ($intoSpan->tagName) {
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
                                        if ($intoSpan->getAttributeNode('style')) {
                                            $temp = str_replace('color:', '', $intoSpan->getAttributeNode('style')->value);
                                        }
                                        $color = str_replace(";", "", $temp ?? '#333452');
                                        break;
                                    case('s'):
                                        $listenable = 1;
                                        break;
                                    default:
                                        break;
                                }
                                if (isset($value) && $value <> "") {
                                    [$intoSpan, $value, $color, $cursive, $bold, $listenable, $underline] = $descriptionFunctions->recursiveTagDataGet($intoSpan, $value, $color, $cursive, $bold, $listenable, $underline);
                                    $subArray[] = [
                                        'value' => $value,
                                        'color' => $color,
                                        'cursive' => $cursive,
                                        'bold' => $bold,
                                        'listenable' => $listenable,
                                        'underline' => $underline
                                    ];
                                }
                            }else
                            if (isset($value) && $value <> "") {
                                [$intoSpan, $value, $color, $cursive, $bold, $listenable, $underline] = $descriptionFunctions->recursiveTagDataGet($intoSpan, $value, $color, $cursive, $bold, $listenable, $underline);
                                $subArray[] = [
                                    'value' => $value,
                                    'color' => $color,
                                    'cursive' => $cursive,
                                    'bold' => $bold,
                                    'listenable' => $listenable,
                                    'underline' => $underline
                                ];
                            }
                            if (count($subArray) <> 0) {
                                $array[] = [
                                    'id' => $key,
                                    'sentenceWords' => $subArray,
                                    'listenable' => $listenable
                                ];
                            }
                        }
                    } else {
                        $subArray = [];
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
                                    $color = str_replace(";", "", $temp ?? '#333452');
                                    break;

                                case('s'):
                                    $listenable = 1;
                                    break;
                                default:
                                    break;
                            }
                            $subArray[] = [
                                'value' => $value,
                                'color' => $color,
                                'cursive' => $cursive,
                                'bold' => $bold,
                                'listenable' => $listenable,
                                'underline' => $underline
                            ];
                        } else {
                            $subArray[] = [
                                'value' => utf8_decode($c->nodeValue),
                                'color' => '#333452',
                                'cursive' => 0,
                                'bold' => 0,
                                'listenable' => 0,
                                'underline' => 0
                            ];
                        }
                        $array[] = [
                            'id' => $key,
                            'sentenceWords' => $subArray,
                            'listenable' => $listenable
                        ];
                    }
                }

            }
        }
        return $array;
    }
}
