<?php

namespace App\Http\Controllers\Services\Parser;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\Text;
use DOMDocument;
use Illuminate\Http\Request;

class PracticeText extends Controller
{
    private $indexOfMedia = 0;

    public function PracticeTextPageGet($mediaText, $file)
    {
        $doc = new DOMDocument();
        $arrayOfSentences = [];
        $imagesData = [];
        $contentOrder = [];
        $html = $mediaText;
        $mediaArray = json_decode($file->url);
        if (isset($html)) {
            foreach ($html as $multiply => $item) {
                $item = explode('<br>', $item);
                foreach ($item as $key => $value) {
                    $multiplyed = 15 * $multiply + $key;
                    $doc->loadHTML($value);
                    $child = $this->hasChild($doc, $multiplyed, $mediaArray);
                    $arrayOfSentences[] = [
                        "id" => $multiplyed,
                        "items" => [$child[0]],
                    ];
                    $imagesData[] = $child[1] ?? null;
                    $contentOrder[] = $child[2] ?? null;
                }
            }
        }
        $imagesDataRefactor = [];
        foreach ($imagesData as $item) {
            if (isset($item['images_data']) && !empty($item['images_data'])) {
                $imagesDataRefactor[] = $item;
            }
        }
        return [
            'text_data' => $arrayOfSentences,
            'images_data' => $imagesDataRefactor,
            'content_order' => $contentOrder
        ];
    }

    public function hasChild($p, $index, $mediaArray)
    {
        $arrayOfSentences = [];
        $arrayOfMedia = [];
        $contentOrder = [];
        $listenable = 0;
        if ($p->hasChildNodes()) {
            foreach ($p->childNodes as $c) {
                $value = utf8_decode($c->nodeValue);
                $color = '#333452';
                $cursive = 0;
                $bold = 0;
                $listenable = 0;
                $underline = 0;
                if ($value == "@@") {
                    $contentOrder[] = [
                        'id' => $index,
                        'type' => 1
                    ];

                    $arrayOfMedia [] = [
                        'id' => $index,
                        'image_url' => $mediaArray[$this->indexOfMedia]
                    ];
                    $this->indexOfMedia += 1;

                } else {
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
                        $contentOrder[] = [
                            'id' => $index,
                            'type' => 0
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
                            $contentOrder[] = [
                                'id' => $index,
                                'type' => 0
                            ];
                        }/* else {
                        $arrayOfSentences[] = [
                            'value' => utf8_decode($c->nodeValue),
                            'color' => '#333452',
                            'cursive' => 0,
                            'bold' => 0,
                            'listenable' => 0,
                            'underline' => 0
                        ];

                    }*/

                    }
                }
            }
        }


        return [
            [
                'id' => $index,
                'sentenceWords' => $arrayOfSentences,
                "listenable" => $listenable
            ],
            [
                "images_data" => $arrayOfMedia
            ],

            $contentOrder[0]

        ];
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
