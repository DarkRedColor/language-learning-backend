<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfoJson extends Model
{
    use HasFactory;
    protected $guarded = [
        'id',
    ];
    protected $table = 'infos_jsons';

    public function subChapters(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(SubChapter::class, 'infos_json_sub_chapter');
    }
}
