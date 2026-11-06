<?php

namespace App\Models;

use App\Http\Controllers\Services\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Introduction extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    public function addNewLanguage($columnLanguage)
    {
        $modelFunctions = new Models();
        $modelFunctions->addNewLanguageForOneColumn("name_", $columnLanguage, 'introductions');

    }

    public function deleteLanguage($columnLanguage)
    {
        $modelFunctions = new Models();
        $modelFunctions->deleteLanguageForOneColumn("name_", $columnLanguage, 'introductions');
    }
}
