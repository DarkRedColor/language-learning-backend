<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameDescriptionPivot extends Model
{
    use HasFactory;

    protected $table = 'practice_game_template_learning';

    protected $fillable = [
        'learning_id',
        'practice_id',
        'game_description_id',
        'game_template_id'
    ];
    public $timestamps = false;
}
