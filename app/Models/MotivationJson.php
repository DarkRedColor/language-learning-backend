<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotivationJson extends Model
{
    use HasFactory;
    protected $guarded = [
        'id',
    ];

    public function motivations(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Motivation::class, 'motivation_motivations_json');
    }
}
