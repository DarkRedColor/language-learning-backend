<?php

namespace App\Models;

use App\Http\Controllers\Services\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Word extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];
    protected $casts = [
        'example_list' => 'array'
    ];

    public function addNewLanguage($columnLanguage)
    {
        $modelFunctions = new Models();
        $modelFunctions->addNewLanguageForOneColumn("meaning_", $columnLanguage, 'words');
        $modelFunctions->addNewLanguageForOneColumn("definition_", $columnLanguage, 'words');

    }

    public function deleteLanguage($columnLanguage)
    {
        $modelFunctions = new Models();
        $modelFunctions->deleteLanguageForOneColumn("meaning_", $columnLanguage, 'words');
        $modelFunctions->deleteLanguageForOneColumn("definition_", $columnLanguage, 'words');

    }

    public function subChapters()
    {
        return $this->belongsToMany(SubChapter::class, 'sub_chapter_word', 'word_id', 'sub_chapter_id');
    }

}
