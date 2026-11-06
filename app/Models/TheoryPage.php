<?php

namespace App\Models;

use App\Http\Controllers\Services\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TheoryPage extends Model
{
    use HasFactory;

    const types = [
        0 => 'title',
    ];

    protected $guarded = [
        'id',
    ];

    public function addNewLanguage($columnLanguage)
    {
        $modelFunctions = new Models();
        $modelFunctions->addNewLanguageForOneColumn("text_", $columnLanguage, 'theory_pages');
    }

    public function deleteLanguage($columnLanguage)
    {
        $modelFunctions = new Models();
        $modelFunctions->deleteLanguageForOneColumn("text_", $columnLanguage, 'theory_pages');
    }
}
