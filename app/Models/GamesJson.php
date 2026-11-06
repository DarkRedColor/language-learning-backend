<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GamesJson extends Model
{
    use HasFactory;
    protected $guarded = [
        'id',
    ];

    public function learnings(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Learning::class, 'games_json_learning');
    }
}
