<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntroductionJson extends Model
{
    use HasFactory;
    protected $guarded = [
        'id',
    ];

    public function introductions(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Introduction::class, 'introduction_introductions_json');
    }
}
