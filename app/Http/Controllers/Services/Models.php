<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class Models extends Controller
{
    public function addNewLanguage($columnLanguage, $tableName)
    {
        $columnDescription = "description_" . $columnLanguage;
        $columnName = "name_" . $columnLanguage;

        if (!Schema::hasColumn($tableName, $columnDescription)) {
            Schema::table($tableName, function (Blueprint $table) use ($columnDescription) {
                $table->text($columnDescription)->nullable();
            });
        }
        if (!Schema::hasColumn($tableName, $columnName)) {
            Schema::table($tableName, function (Blueprint $table) use ($columnName) {
                $table->string($columnName)->nullable();
            });
        }

    }

    public function deleteLanguage($columnLanguage, $tableName)
    {
        $columnDescription = "description_" . $columnLanguage;
        $columnName = "name_" . $columnLanguage;

        if (Schema::hasColumn($tableName, $columnDescription)) {

            Schema::table($tableName, function (Blueprint $table) use ($columnDescription) {
                $table->dropColumn($columnDescription);
            });

        }
        if (Schema::hasColumn($tableName, $columnName)) {

            Schema::table($tableName, function (Blueprint $table) use ($columnName) {
                $table->dropColumn($columnName);
            });

        }
    }

    public function addNewLanguageForOneColumn($column, $columnLanguage, $tableName)
    {
        $columnName = $column . $columnLanguage;
        if (!Schema::hasColumn($tableName, $columnName)) {
            Schema::table($tableName, function (Blueprint $table) use ($columnName) {
                $table->longText($columnName)->nullable();
            });
        }
    }

    public function deleteLanguageForOneColumn($column, $columnLanguage, $tableName)
    {
        $columnName = $column . $columnLanguage;
        if (Schema::hasColumn($tableName, $columnName)) {
            Schema::table($tableName, function (Blueprint $table) use ($columnName) {
                $table->dropColumn($columnName);
            });
        }
    }
}
