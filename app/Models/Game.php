<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class Game extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    public function addNewLanguage($columnLanguage)
    {
        $columnDescription = "linked_words_" . $columnLanguage;
        $columnName = "name_" . $columnLanguage;
        $columnMission = 'mission_' . $columnLanguage;
        $columnHint = 'hint_' . $columnLanguage;

        if (!Schema::hasColumn('games', $columnDescription)) {
            Schema::table('games', function (Blueprint $table) use ($columnDescription) {
                $table->text($columnDescription)->nullable();
            });
        }

        if (!Schema::hasColumn('games', $columnName)) {
            Schema::table('games', function (Blueprint $table) use ($columnName) {
                $table->text($columnName)->nullable();
            });
        }

        if (!Schema::hasColumn('games', $columnMission)) {
            Schema::table('games', function (Blueprint $table) use ($columnMission) {
                $table->text($columnMission)->nullable();
            });
        }

        if (!Schema::hasColumn('games', $columnHint)) {
            Schema::table('games', function (Blueprint $table) use ($columnHint) {
                $table->text($columnHint)->nullable();
            });
        }

    }

    public function deleteLanguage($columnLanguage)
    {
        $columnDescription = "linked_words_" . $columnLanguage;
        $columnName = "name_" . $columnLanguage;
        $columnMission = 'mission_' . $columnLanguage;

        if (Schema::hasColumn("games", $columnDescription)) {
            Schema::table('games', function (Blueprint $table) use ($columnDescription) {
                $table->dropColumn($columnDescription);
            });
        }
        if (Schema::hasColumn("games", $columnName)) {
            Schema::table('games', function (Blueprint $table) use ($columnName) {
                $table->dropColumn($columnName);
            });
        }
        if (Schema::hasColumn("games", $columnMission)) {
            Schema::table('games', function (Blueprint $table) use ($columnMission) {
                $table->dropColumn($columnMission);
            });
        }


    }
    public function gamesTemplate(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(GameTemplate::class, 'game_game_template');
    }
    public function practices(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Practice::class, 'game_practice');
    }
    public function theories(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Theory::class, 'game_theory');
    }

}
