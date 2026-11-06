<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SummaryJson extends Model
{
    use HasFactory;
    protected $guarded = [
        'id',
    ];

    public function subChapters(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(SubChapter::class, 'sub_chapter_summaries_json');
    }
}
