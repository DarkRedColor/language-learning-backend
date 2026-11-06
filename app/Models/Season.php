<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Season extends Model
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

    /**
     * Get the episodes for the season.
     */
    public function episodes()
    {
        return $this->hasMany(Episode::class);
    }

    public function chapters()
    {
        return $this->hasManyThrough(Chapter::class, Episode::class);
    }
}
