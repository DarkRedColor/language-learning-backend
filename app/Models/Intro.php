<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class Intro extends Model
{
    use HasFactory;

    protected $table = 'dialogues';

    protected $guarded = [
        'id',
    ];

    protected $casts = [

        'next_id' => 'array',

    ];

    public function addNewLanguage($columnLanguage)
    {
        $columnName = "text_" . $columnLanguage;
        if (!Schema::hasColumn($this->table, $columnName)) {
            Schema::table($this->table, function (Blueprint $table) use ($columnName) {
                $table->text($columnName)->nullable();
            });
        }
    }

    public function deleteLanguage($columnLanguage)
    {
        $columnName = "text_" . $columnLanguage;
        if (Schema::hasColumn($this->table, $columnName)) {
            Schema::table($this->table, function (Blueprint $table) use ($columnName) {
                $table->dropColumn($columnName);
            });
        }
    }

}
