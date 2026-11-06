<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WelcomeJson extends Model
{
    use HasFactory;
    protected $guarded = [
        'id',
    ];

    public function welcomes(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Welcome::class, 'welcome_welcomes_json');
    }
}
