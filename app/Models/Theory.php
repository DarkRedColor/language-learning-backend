<?php

namespace App\Models;

use App\Http\Controllers\Services\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Theory extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'tags' => 'array'
    ];

    public function words()
    {
        return $this->belongsToMany(Word::class, 'theory_word');
    }
    public function theoryPages()
    {
        return $this->hasManyThrough(TheoryPage::class, TheoryPageName::class,'theory_id','theory_page_name_id','id','theory_id');
    }
    public function games(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'game_theory');
    }
    public function addNewLanguage($columnLanguage)
    {
        $modelFunctions = new Models();
        $modelFunctions->addNewLanguageForOneColumn("description_", $columnLanguage, 'theories');
    }

    public function deleteLanguage($columnLanguage)
    {
        $modelFunctions = new Models();
        $modelFunctions->deleteLanguageForOneColumn("description_", $columnLanguage, 'theories');
    }
//    public function users()
//    {
//        return $this->hasManyThrough(
//            'App\Models\User',
//            'App\Models\UserGroupDue',
//            'user_id',
//            'id',
//            'name',
//            'group_name'
//        );
//    }
//    public function users()
//    {
//        return $this->hasManyThrough(
//            'App\Models\User',
//            'App\Models\UserGroupDue',
//            'user_id',
//            'group_name',
//        'id',
//        'name',
//    );
//        }
}
