<?php

namespace App\Imports;

use App\Models\Background;
use App\Models\Intro;
use App\Models\Language;
use App\Models\Personage;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpsertColumns;

class NewDialogueImport implements ToCollection, WithUpsertColumns, WithHeadingRow
{
    use Importable;

    private $arr = [
        'id',
        'content_type',
        'parent_id',
        'background_id',
        'position',
        'personage_id',
        'emotion_id',
        'text_en'
    ];

    private $content_type = [
        'line' => 0,
        'choice' => 3,
        'action' => 1,
        'notification' => 2,
    ];

    private $position = [
        'left' => 0,
        'right' => 1,
    ];

    private $personage_id;
    private $subchapter_id;
    private $choices;

    public function __construct($id)
    {
        $this->subchapter_id = $id;
    }

    public function collection(Collection $rows)
    {
        $arr = $this->arr;
        $dialogues = [];
        foreach ($rows as $row) {
            $dialogue = [];
            foreach ($arr as $item) {
                if ($row->has($item)) {
                    if ($row['id'] <> null) {
                        switch ($item) {
                            case('id'):
                                if ($row['id'] <> null) {
                                    $dialogue['id'] = $row['id'];
                                }
                                break;
                            case('content_type'):
                                if ($row['content_type'] <> null) {
                                    $dialogue['content_type'] = $this->content_type[$row['content_type']];
                                }
                                break;
                            case('parent_id'):
                                $dialogue['parent_id'] = $row['parent_id'];
                                break;
                            case('background_id'):
                                $dialogue['background_id'] = $this->backgroundIdSet($row["background_id"]);
                                break;
                            case('position'):
                                if ($row['position'] <> null) {
                                    $dialogue['position'] = $this->position[$row['position']];
                                }
                                break;
                            case('personage_id'):
                                $dialogue['personage_id'] = $this->personageIdSet($row["personage_id"]);
                                break;
                            case('emotion_id'):
                                $dialogue['emotion_id'] = $this->emotionIdSet($row["emotion_id"]);
                                break;
                            case('text_en'):
                                $dialogue['text_en'] = $row['text_en'];
                                break;
                            default:
                                break;
                        }
                    }
                }
            }
            if (isset($dialogue) && !empty($dialogue)) {
                $dialogues[] = $dialogue;
            }
        }

        $this->dialoguesWriteToDataBase($dialogues);
        $newDialogue = Intro::where('sub_chapter_id', $this->subchapter_id)->get();
        $subChoices = $newDialogue->where('content_type', 3)->whereNotNull('parent_id');
        $this->orderSet($subChoices, $newDialogue);

    }

    private function orderSet($elems, $newDialogue)
    {
        foreach ($elems as $item) {
            $array = $newDialogue->where('parent_id', $item->id)->values();
            $ids = null;
            foreach ($array->pluck('id')->toArray() as $val) {
                $ids[] = ["$val"];
            }
            Intro::find($item->id)->update([
                'next_id' => $ids
            ]);
            foreach ($array as $key => $value) {
                if (count($array) == $key + 1) {
                    Intro::find($value->id)->update([
                        'order' => $key,
                        'next_id' => null,
                    ]);
                } else {
                    $next_id = $array[$key + 1]->id;
                    Intro::find($value->id)->update([
                        'order' => $key,
                        'next_id' => [["$next_id"]],
                    ]);
                }
            }
        }

    }

    private function dialoguesWriteToDataBase($dialogues)
    {
        $choice = [];
        $i = 0;
        foreach ($dialogues as $key => $item) {
            if ($item['parent_id'] == null) {
                $intro = Intro::create([
                    'emotion_id' => $item['emotion_id'],
                    'personage_id' => $item['personage_id'],
                    'position' => $item['position'],
                    'order' => $i,
                    'content_type' => $item['content_type'],
                    'parent_id' => $item['parent_id'],
                    'text_en' => $item['text_en'],
                    'sub_chapter_id' => $this->subchapter_id,
                    'background_id' => $item['background_id'],
                ]);
                if ($item['content_type'] == 3) {
                    $choice = [];
                    $choice['parent'] = $intro->id;
                    $choice['order'] = 0;
                    $this->choices = null;
                }
                if ($key <> 0) {
                    Intro::find($intro->id - 1)->update([
                        'next_id' => [["$intro->id"]]
                    ]);
                }
                $i++;
            } else {
                if ($item['content_type'] == 3) {
                    $choice['order'] = $this->choiceBuild($item, $choice);
                } else {
                    Intro::create([
                        'emotion_id' => $item['emotion_id'],
                        'personage_id' => $item['personage_id'],
                        'position' => $item['position'],
                        'content_type' => $item['content_type'],
                        'parent_id' => $this->choices[$item['parent_id']],
                        'text_en' => $item['text_en'],
                        'sub_chapter_id' => $this->subchapter_id,
                        'background_id' => $item['background_id'],

                    ]);
                }
            }
        }
    }

    private function choiceBuild($item, $choiceParent)
    {
        $intro = Intro::create([
            'emotion_id' => $item['emotion_id'],
            'personage_id' => $item['personage_id'],
            'position' => $item['position'],
            'order' => $choiceParent['order'],
            'content_type' => $item['content_type'],
            'parent_id' => $choiceParent['parent'],
            'text_en' => $item['text_en'],
            'sub_chapter_id' => $this->subchapter_id,
            'background_id' => $item['background_id'],

        ]);

        $this->choices[$item['id']] = $intro->id;
        $parent = Intro::find($choiceParent['parent']);
        $next_id = $parent['next_id'];
        if ($next_id == null) {
            $parent->update([
                'next_id' => [["$intro->id"]]
            ]);
        } else {
            $next_id [] = ["$intro->id"];
            $parent->update([
                'next_id' => $next_id
            ]);
        }

        return $choiceParent['order'] + 1;
    }

    private function backgroundIdSet($item)
    {
        $background = Background::where('name', $item)->first();
        if (isset($background)) {
            $background_id = $background->id;
        } else {
            $background_id = null;
        }
        return $background_id;
    }

    private function personageIdSet($item)
    {
        $personage = Personage::where('name_en', $item)->first();
        if (isset($personage)) {
            $personage_id = $personage->id;
        } else {
            $personage_id = null;
        }

        $this->personage_id = $personage_id;

        return $personage_id;
    }

    private function emotionIdSet($item)
    {
        $personage_id = $this->personage_id;
        $personage = Personage::with('emotions')->find($personage_id);
        if (isset($personage)) {
            $emotion = $personage->emotions->where('name', $item)->first();
            if (isset($emotion)) {
                $emotion_id = $emotion->id;
            } else {
                $emotion_id = null;
            }
        } else {
            $emotion_id = null;
        }
        $this->personage_id = null;

        return $emotion_id;
    }

    public function upsertColumns()
    {
        return 'text_en';
    }
}
