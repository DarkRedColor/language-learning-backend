<?php

namespace App\Models;

use App\Http\Controllers\Services\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Motivation extends Model
{
    use HasFactory;
    protected $guarded = [
        'id',
    ];

    public function addNewLanguage($columnLanguage)
    {
        $modelFunctions = new Models();
        $modelFunctions->addNewLanguageForOneColumn("name_", $columnLanguage, 'motivations');

    }

    public function deleteLanguage($columnLanguage)
    {
        $modelFunctions = new Models();
        $modelFunctions->deleteLanguageForOneColumn("name_", $columnLanguage, 'motivations');
    }
}
