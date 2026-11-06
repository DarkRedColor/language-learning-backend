<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TheoryJson extends Model
{
    use HasFactory;
    protected $guarded = [
        'id',
    ];

    public function theories(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Theory::class, 'theories_json_theory');
    }
}
