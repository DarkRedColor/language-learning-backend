<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Emotion extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    public function personage()
    {
        return $this->belongsToMany(Personage::class, 'emotion_personage');
    }
}
