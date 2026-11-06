<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Learning extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    public function theories(): BelongsToMany
    {
        return $this->belongsToMany(Theory::class, 'learning_theory');
    }

    public function practices(): BelongsToMany
    {
        return $this->belongsToMany(Practice::class, 'learning_practice');
    }

    public function learningPractices(): BelongsToMany
    {
        return $this->belongsToMany(Practice::class, 'practice_game_template_learning');
    }

    public function learningGameDescriptions(): BelongsToMany
    {
        return $this->belongsToMany(GameDescription::class, 'practice_game_template_learning');
    }

    public function learningGameTemplates(): BelongsToMany
    {
        return $this->belongsToMany(GameTemplate::class, 'practice_game_template_learning');
    }

    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'game_learning');
    }

    public function gamesJson(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(GamesJson::class, 'games_json_learning');
    }
}
