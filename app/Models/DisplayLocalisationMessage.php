<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class DisplayLocalisationMessage extends Model
{
    use HasFactory;

    public function addNewLanguage($columnLanguage)
    {
        $columnDescription = "description_" . $columnLanguage;
        $columnName = "name_" . $columnLanguage;
        Schema::table('display_localisation_messages', function (Blueprint $table) use ($columnName,$columnDescription) {
            $table->text($columnDescription)->nullable();
            $table->text($columnName)->nullable();
        });
    }
    public function deleteLanguage($columnLanguage)
    {
        $columnDescription = "description_" . $columnLanguage;
        $columnName = "name_" . $columnLanguage;
        Schema::table('display_localisation_messages', function (Blueprint $table) use ($columnName,$columnDescription) {
            $table->dropColumn($columnDescription);
            $table->dropColumn($columnName);
        });
    }
}
