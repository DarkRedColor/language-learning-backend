<?php

namespace App\Models;

use App\Http\Controllers\Services\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Personage extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    public function emotions()
    {

        return $this->belongsToMany(Emotion::class, 'emotion_personage');

    }

    public function addNewLanguage($columnLanguage)
    {
        $modelFunctions = new Models();
        $modelFunctions->addNewLanguageForOneColumn("name_", $columnLanguage, 'personages');
    }

    public function deleteLanguage($columnLanguage)
    {
        $modelFunctions = new Models();
        $modelFunctions->deleteLanguageForOneColumn("name_", $columnLanguage, 'personages');
    }

}
