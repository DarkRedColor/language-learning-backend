<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Chapter extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id',
    ];

    // chapter type
    // 0 -> default
    // 1 -> quiz
    // 2 -> intro

    /**
     * Get the vocabularies for the chapter.
     */
    public function vocabularies()
    {
        return $this->hasMany(Vocabulary::class);
    }

    /**
     * Get the stories for the chapter.
     */
    public function stories()
    {
        return $this->hasMany(Story::class);
    }

    /**
     * Get the grammars for the chapter.
     */
    public function grammars()
    {
        return $this->hasMany(Grammar::class);
    }

    /**
     * Get the post that owns the comment.
     */
    public function episode()
    {
        return $this->belongsTo(Episode::class);
    }
}
