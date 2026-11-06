<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\Practice;
use App\Models\Game;
use App\Models\GameTemplate;
use App\Models\Learning;
use Illuminate\Http\Request;

class PracticeApiController extends Controller
{

//    public function typeGetByString($string)
//    {
//        switch ($string) {
//            case ("eruditeGames"):
//                $type = 0;
//                break;
//            case ("millionaireGames"):
//                $type = 1;
//                break;
//            case ("linkWordsGames"):
//                $type = 2;
//                break;
//            case ("typeAnswerGames"):
//                $type = 3;
//                break;
//            case ('makeASentenceGames'):
//                $type = 4;
//                break;
//            case ('insertWordsGames'):
//                $type = 5;
//                break;
//            case ('typeWordsGames'):
//                $type = 6;
//                break;
//            default:
//                $type = null;
//        }
//        return $type;
//    }

    public function practiceGet($learning, $size, $lcid, $randomLocalise, $games = [],$gameTemplate = [],$practiceArr = [])
    {
//        $learning = Learning::with(['games'])->find($learning->id);

        $practicesWithoutGames = $learning->practices;

        $practiceFunctions = new Practice();

        $practicesWithGames = [];
        $learningPracticesGamesArray = [];

        $res = [];

        foreach ($practicesWithoutGames as $item) {
            if (array_key_exists($item->id,$practiceArr)){
                $practicesWithGames[] = $practiceArr[$item->id];
            }else{
                $practicesWithGames[] = $practiceArr[$item->id] = \App\Models\Practice::with('games','gameTemplates')->find($item->id);

            }
        }

        if (isset($learning)) {


            foreach ($practicesWithGames as $gamesArr) {


                if (isset($gamesArr->games)) {
                    $gamesArray = [];
                    foreach ($gamesArr->games as $item) {

                        foreach ($learning->games as $game) {
                            if ($item->id == $game->id) {
                                $gamesArray[] = $game;
                            }

                        }
                    }
                    $learningPracticesGamesArray[] = $gamesArray;
                }
            }
            foreach ($learningPracticesGamesArray as $gameArr) {

                $allGamesApiResponse = [];
                $eruditeGames = [];
                $millionaireGames = [];
                $linkWordsGames = [];
                $typeAnswerGames = [];
                $makeASentenceGames = [];
                $insertWordsGames = [];
                $removeWordsGames = [];
                $typeWordsGames = [];
                $trueOrFalseGames = [];
                $insertNumbersGames = [];
                $insertSymbolsGames = [];
                $theories = null;


                foreach ($gameArr as $item) {
                    $theories = $learning->theories;
                    if (array_key_exists($item->id, $games)) {
                        $gameWithGameTemplate = $games[$item->id];
                    } else {
                        $gameWithGameTemplate = $games[$item->id] = Game::with('gamesTemplate', 'theories', 'practices')->find($item->id);
                    }
                    $templateId = $gameWithGameTemplate->gamesTemplate[0]->id;
                    [$type,$gameTemplate] = $practiceFunctions->typeGet($templateId,$gameTemplate);
                    if (isset($gameWithGameTemplate->theories) && !empty($gameWithGameTemplate->theories) && count($gameWithGameTemplate->theories) <> 0) {
                        $theories = $gameWithGameTemplate->theories;
                    }
                    switch ($type) {
                        case (0):
                            if ($lcid == 'en') {
                                if ($gameWithGameTemplate->gamesTemplate[0]->task <> 'text') {
                                    $eruditeGames[] = $practiceFunctions->eruditeGameDataBuild($gameWithGameTemplate, $theories);
                                }
                            } else {
                                $eruditeGames[] = $practiceFunctions->eruditeGameDataBuild($gameWithGameTemplate, $theories);
                            }
                            break;
                        case (1):
                            if ($lcid == 'en') {
//                                if ($item->id == 10804)
//                                    dd([1=> $gameWithGameTemplate->input_type == 1 && $gameWithGameTemplate->gamesTemplate[0]->task <> 'text'],[2=>$gameWithGameTemplate->input_type <> 1 && $gameWithGameTemplate->gamesTemplate[0]->task == 'text'],[3=>$gameWithGameTemplate->input_type <> 1 && $gameWithGameTemplate->gamesTemplate[0]->task <> 'text'],[$gameWithGameTemplate->input_type,$gameWithGameTemplate->gamesTemplate[0]->task]);
                                if ($gameWithGameTemplate->gamesTemplate[0]->task == 'space')
                                    $millionaireGames[] = $practiceFunctions->millionaireGameDataBuild($gameWithGameTemplate, $theories, $size, $lcid, 0);
                                elseif ($gameWithGameTemplate->input_type == 1 && $gameWithGameTemplate->gamesTemplate[0]->task <> 'text') {
                                    $millionaireGames[] = $practiceFunctions->millionaireGameDataBuild($gameWithGameTemplate, $theories, $size, $lcid, 0);
                                } else {
                                    if ($gameWithGameTemplate->input_type <> 1 && $gameWithGameTemplate->gamesTemplate[0]->task == 'text') {
                                        $millionaireGames[] = $practiceFunctions->millionaireGameDataBuild($gameWithGameTemplate, $theories, $size, $lcid, 0);
                                    } else {
                                        if ($gameWithGameTemplate->input_type <> 1 && $gameWithGameTemplate->gamesTemplate[0]->task <> 'text') {
                                            $millionaireGames[] = $practiceFunctions->millionaireGameDataBuild($gameWithGameTemplate, $theories, $size, $lcid, 0);
                                        }
//                                            $millionaireGames[] = $practiceFunctions->millionaireGameDataBuild($gameWithGameTemplate, $theories, $size, $lcid, 0);
                                    }
                                }
                            } else {
                                $millionaireGames[] = $practiceFunctions->millionaireGameDataBuild($gameWithGameTemplate, $theories, $size, $lcid, $randomLocalise);
                            }
                            break;
                        case (2):
                            if ($lcid == 'en') {
                                if ($gameWithGameTemplate->input_type == 1 && $gameWithGameTemplate->gamesTemplate[0]->additional_task <> 'text') {
                                    [$linkWordsGames[],$gameTemplate] = $practiceFunctions->linkWordsGameDataBuild($gameWithGameTemplate, $theories, $lcid, $gameTemplate);
                                } else {
                                    if ($gameWithGameTemplate->input_type <> 1 && $gameWithGameTemplate->gamesTemplate[0]->additional_task == 'text') {
                                        [$linkWordsGames[],$gameTemplate] = $practiceFunctions->linkWordsGameDataBuild($gameWithGameTemplate, $theories, $lcid, $gameTemplate);
                                    }
                                }
                            } else {
                                [$linkWordsGames[],$gameTemplate] = $practiceFunctions->linkWordsGameDataBuild($gameWithGameTemplate, $theories, $lcid, $gameTemplate);
                            }
                            break;
                        case (3):
                            if ($lcid == 'en') {
                                if ($gameWithGameTemplate->gamesTemplate[0]->task <> 'text') {
                                    $typeAnswerGames[] = $practiceFunctions->typeAnswerGameDataBuild($gameWithGameTemplate, $theories, $lcid);
                                }
                            } else {
                                $typeAnswerGames[] = $practiceFunctions->typeAnswerGameDataBuild($gameWithGameTemplate, $theories, $lcid);
                            }
                            break;
                        case (4):
                            if ($lcid == 'en') {
                                if ($gameWithGameTemplate->gamesTemplate[0]->task <> 'text') {
                                    $makeASentenceGames[] = $practiceFunctions->makeASentenceGameDataBuild($gameWithGameTemplate, $theories);
                                }
                            } else {
                                $makeASentenceGames[] = $practiceFunctions->makeASentenceGameDataBuild($gameWithGameTemplate, $theories);
                            }
                            break;
                        case (5):
                            $insertWordsGames[] = $practiceFunctions->insertWordsGameDataBuild($gameWithGameTemplate, $theories);
                            break;
                        case (6):
                            $typeWordsGames[] = $practiceFunctions->typeWordsGameDataBuild($gameWithGameTemplate, $theories);
                            break;
                        case (9):
                            $trueOrFalseGames[] = $practiceFunctions->trueOrFalseDataBuild($gameWithGameTemplate, $theories);
                            break;
                        case (7):
                            $insertSymbolsGames[] = $practiceFunctions->insertSymbolsDataBuild($gameWithGameTemplate, $theories);
                            break;
                        case (8):
                            $insertNumbersGames[] = $practiceFunctions->insertSymbolsDataBuild($gameWithGameTemplate, $theories);
                            break;
                        case (10):
                            $removeWordsGames[] = $practiceFunctions->removeWordsDataBuild($gameWithGameTemplate, $theories);
                            break;
                        default:
                            break;
                    }
                }
                $allGamesApiResponse['eruditeGames'] = $eruditeGames;
                $allGamesApiResponse['millionaireGames'] = $millionaireGames;
                $allGamesApiResponse['linkWordsGames'] = $linkWordsGames;
                $allGamesApiResponse['typeAnswerGames'] = $typeAnswerGames;
                $allGamesApiResponse['makeASentenceGames'] = $makeASentenceGames;
                $allGamesApiResponse['insertWordsGames'] = $insertWordsGames;
                $allGamesApiResponse['typeWordsGames'] = $typeWordsGames;
                $allGamesApiResponse['trueOrFalseGames'] = $trueOrFalseGames;
                $allGamesApiResponse['insertSymbolsGames'] = $insertSymbolsGames;
                $allGamesApiResponse['insertNumbersGames'] = $insertNumbersGames;
                $allGamesApiResponse['removeWordsGames'] = $removeWordsGames;
                $res[] = $allGamesApiResponse;
            }
        }

        return [$res, $games,$gameTemplate,$practiceArr];

    }
}
