<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class SubChapter extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'tags' => 'array',
        'learnings' => 'array',
        'other'=>'array',
    ];

    public function addNewLanguage($columnLanguage)
    {
        $columnName = "name_" . $columnLanguage;
        $columnDescription = "description_" . $columnLanguage;

        Schema::table('sub_chapters', function (Blueprint $table) use ($columnName, $columnDescription) {
            $table->string($columnName)->nullable();
            $table->string($columnDescription)->nullable();

        });
    }

    public function deleteLanguage($columnLanguage)
    {
        $columnName = "name_" . $columnLanguage;
        $columnDescription = "description_" . $columnLanguage;

        Schema::table('sub_chapters', function (Blueprint $table) use ($columnName, $columnDescription) {
            $table->dropColumn($columnName);
            $table->dropColumn($columnDescription);
        });
    }

    public function words()
    {
        return $this->belongsToMany(Word::class, 'sub_chapter_word');
    }

    public function infoJson(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(InfoJson::class, 'infos_json_sub_chapter');
    }

    public function dialogueJson(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(DialogueJson::class, 'dialogues_json_sub_chapter');
    }
}
