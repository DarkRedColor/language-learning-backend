<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ArcadeGame extends Model
{
    use HasFactory;
    protected $guarded = [
        'id',
    ];
    public function arcadeThemes(): BelongsToMany
    {
        return $this->belongsToMany(ArcadeTheme::class, 'arcade_game_arcade_theme');
    }
}
