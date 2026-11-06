<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\GameWord;
use App\Models\Word;
use Illuminate\Http\Request;

class Text extends Controller
{
    public function onlyLocalisationGet($lcid, $field, $model)
    {
        $englishField = $field . "_en";
        $res = null;
        if (isset($model)) {
            $res = $model->$englishField;
            if ($lcid != 'en') {
                $localisateField = $field . "_$lcid";
                if (isset($model->$localisateField)) {
                    $res = $model->$localisateField;
                }
            }
        }
        return $res;
    }

    public function onlyLocalisationGameGet($lcid, $field, $model)
    {
        $englishField = $field . "_en";
        $res = $model->$englishField;
        if ($model->input_type == 0) {
            if ($lcid != 'en') {
                $localisateField = $field . "_$lcid";
                if (isset($model->$localisateField)) {
                    $res = $model->$localisateField;
                }
            }
        } else {
            $wordId = GameWord::where('game_id', $model->id)->where('type', 0)->first();
            if (isset($wordId)) {
                $wordId = $wordId->word_id;
                $res = Word::find($wordId)->word;
                if ($lcid != 'en') {
                    $localisateField = 'meaning' . "_$lcid";
                    if (isset(Word::find($wordId)->$localisateField)) {
                        $res = Word::find($wordId)->$localisateField;
                    }
                }
            } else {
                $wordId = GameWord::where('game_id', $model->id)->where('type', 1)->first();
                if (isset($wordId)) {
                    $wordId = $wordId->word_id;
                    $res = Word::find($wordId)->word;
                    if ($lcid != 'en') {
                        $localisateField = 'meaning' . "_$lcid";
                        if (isset(Word::find($wordId)->$localisateField)) {
                            $res = Word::find($wordId)->$localisateField;
                        }
                    }
                }
            }
        }

        return $res;
    }

    public function unicalCheck($texts, $key, $i, $j, $name, $riv = false)
    {
        if ($riv) {
            $key .= ".riv";
        }

        if ($texts->where($name, $key)->first() <> null) {
            if ($texts->where($name, $key)->first()->$name <> null) {
                if ($riv) {
                    if ($key[strlen($key) - ($j + 4)] == "_") {
                        $key = substr($key, 0, -($j + 4));
                    }
                } elseif ($key[strlen($key) - $j] == "_") {
                    $key = substr($key, 0, -$j);
                }
                $key = $key . "_" . $i;
                $i += 1;
                if (100 > $i && $i > 10) {
                    $j = 3;
                } elseif (10 < $i) {
                    $j = 4;
                }

                $key = $this->unicalCheck($texts, str_replace('.riv', '', $key), $i, $j, $name, $riv);

            }
        }
        return $key;
    }
}
