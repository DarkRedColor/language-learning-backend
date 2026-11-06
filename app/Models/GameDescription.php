<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class GameDescription extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    public function addNewLanguage($columnLanguage)
    {
        $columnDescription = "description_" . $columnLanguage;
        if (!Schema::hasColumn('game_descriptions', $columnDescription)) {

            Schema::table('game_descriptions', function (Blueprint $table) use ($columnDescription) {
                $table->text($columnDescription)->nullable();
            });
        }
    }

    public function deleteLanguage($columnLanguage)
    {
        $columnDescription = "description_" . $columnLanguage;

        if (Schema::hasColumn('game_descriptions', $columnDescription)) {
            Schema::table('game_descriptions', function (Blueprint $table) use ($columnDescription) {
                $table->dropColumn($columnDescription);
            });
        }
    }
}
