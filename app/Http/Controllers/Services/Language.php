<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\GameDescription;
use App\Models\Intro;
use App\Models\Introduction;
use App\Models\Motivation;
use App\Models\Personage;
use App\Models\Text;
use App\Models\Theory;
use App\Models\TheoryPage;
use App\Models\Welcome;


class Language extends Controller
{
    public function langCreate($item)
    {
        $models = new Models();
        $models->addNewLanguage($item['lcid'], "seasons");

        $models->addNewLanguage($item['lcid'], "episodes");

        $models->addNewLanguage($item['lcid'], "chapters");

        $models->addNewLanguage($item['lcid'], "display_localisation_messages");

        $models->addNewLanguage($item['lcid'], "sub_chapters");

        $theory = new Theory();
        $theory->addNewLanguage($item['lcid']);

        $game = new Game();
        $game->addNewLanguage($item['lcid']);

        $text = new Text();
        $text->addNewLanguage($item['lcid']);

        $text = new Welcome();
        $text->addNewLanguage($item['lcid']);

        $text = new Motivation();
        $text->addNewLanguage($item['lcid']);

        $text = new Introduction();
        $text->addNewLanguage($item['lcid']);

        $theoryPages = new TheoryPage();
        $theoryPages->addNewLanguage($item['lcid']);

        $personages = new Personage();
        $personages->addNewLanguage($item['lcid']);

        $gameDescription = new GameDescription();
        $gameDescription->addNewLanguage($item['lcid']);

        $intro = new Intro();
        $intro->addNewLanguage($item['lcid']);

    }

    public function getActiveLanguage()
    {
       return \App\Models\Language::where('deleted', 1)->get();

    }
}
