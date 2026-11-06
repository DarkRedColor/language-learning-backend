<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DialogueJson extends Model
{
    use HasFactory;
    protected $guarded = [
        'id',
    ];
    protected $table = 'dialogues_jsons';


    public function subChapters(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(SubChapter::class, 'dialogues_json_sub_chapter');
    }
}
