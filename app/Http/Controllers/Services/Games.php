<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\GameType;
use App\Models\Theory;
use Illuminate\Http\Request;
use Str;

class Games extends Controller
{
    public function descriptionDouplicatEliminate($description, $item)
    {
        $elim = 0;
        if (isset($description) && !empty($description)) {
            foreach ($description as $descr) {
                if ($descr['id'] == $item) {
                    $elim = 1;
                }
            }
        }
        if ($elim == 0) {
            return $item;
        } else {
            return null;
        }


    }

    public function list($lists, $languages)
    {
        $list[0] = [];
        $list[1] = [];
        $list[2] = [];
        $list[3] = [];
        $i = 0;
        $j = 0;

        foreach ($lists as $item) {
            $nullable = [];

            foreach ($languages as $language) {
                $name_ = "name_" . $language->lcid;
                if (!$item->$name_ == null) {
                    $list[1][$j] = '';
                    $i += 1;
                } else {
                    $nullable [] = $language->lcid;
                }
                $list[0][$j] = $i;
            }
            $nullable = json_encode($nullable, JSON_UNESCAPED_UNICODE);
            $nullable = Str::replace(['"', '[', ']'], '', $nullable);
            $list[1][$j] = $nullable;
            $j += 1;
            $i = 0;
        }

        $z = 0;
        $x = 0;
        foreach ($lists as $item) {
            $nullable = [];
            foreach ($languages as $language) {
                $description_ = "description_" . $language->lcid;
                if (!$item->$description_ == null) {
                    $list[3][$x] = '';
                    $z += 1;
                } else {
                    $nullable [] = $language->lcid;
                }
                $list[2][$x] = $z;
            }
            $nullable = json_encode($nullable, JSON_UNESCAPED_UNICODE);
            $nullable = Str::replace(['"', '[', ']'], '', $nullable);
            $list[3][$x] = $nullable;
            $x += 1;
            $z = 0;
        }

        return $list;
    }

    public function cluesArrayBuild($tasks, $clues): array
    {
        $cluesBiArr = [];
        $clues = $this->cluesWithoutBraces($clues);
        if (isset($tasks)) {
            foreach ($tasks as $increment => $task) {
                $message = explode(" ", $task);
                $arr = [];
                $i = 0;
                foreach ($message as $value) {
                    if ($value == '@$' || $value == '$' || $value == '$.' || $value == '@$.') {
                        $arr[] = $clues[$increment][$i];
                        $i++;
                    }

                }
                $cluesBiArr[] = $arr;

            }
        }

        return $cluesBiArr;

    }

    public function deleteAdditionalSimbolsFromTask($tasks)
    {
        $newTasksAux = [];
        $newTasks = [];
        if (isset($tasks)) {
            foreach ($tasks as $incriment => $item) {
                $message = explode(" ", $item);
                foreach ($message as $key => $value) {
                    if (in_array($value, ['@$', '@', '@ $',])) {
                        $newTasksAux[$incriment][] = '@';
                    } elseif (in_array($value, ['$.', '@$.',])) {
                        $newTasksAux[$incriment][$key - 1] = '@.';
                    } else {
                        $newTasksAux[$incriment][] = $value;
                    }
                }
                $newTasks [] = implode(' ', $newTasksAux[$incriment]);
            }
        }
        return $newTasks;
    }

    public function doubleAnswerCheck($answers): array
    {
        $arrOfAnswers = [];
        if (isset($answers)) {
            foreach ($answers as $key => $answer) {
                $arrOfAnswers[$key] = explode('#', Str::replace('/', '#', $answer));
            }
        }
        return $arrOfAnswers;
    }

    public function taskBuildForEdit($tasks, $answers, $clues)
    {
        $tasks = json_decode($tasks);
        $answers = json_decode($answers);
        $clues = json_decode($clues);
        $newTask = [];
        $incanswers = 0;
        if (isset($tasks)) {
            foreach ($tasks as $key => $task) {
                $buildedTask = null;
                $message = explode(' ', $task);
                $incClues = 0;
                foreach ($message as $incriment => $word) {
                    $message[$incriment] = Str::replace('⁠', '', $word);
                }
                foreach ($message as $word) {
                    if (Str::contains($word, "@'")) {
                        if (array_key_exists(1, $answers[$incanswers])) {
                            $buildedTask = $this->multipleAnswerBuild($answers[$incanswers], $buildedTask);
                        } else {
                            $explodedParts = explode("'", $word);
                            $buildedTask = $buildedTask . "<i>" . $answers[$incanswers][0] . "</i>'$explodedParts[1] ";
                        }
                        if (isset($clues)) {
                            if (isset($clues[$key])) {
                                if (isset($clues[$key][$incClues])) {
                                    $buildedTask = $buildedTask . "{" . $clues[$key][$incClues] . "} ";
                                }
                            }
                        }
                        $incClues++;
                        $incanswers++;
                    } else {
                        switch ($word) {
                            case ('@'):
                                if (array_key_exists(1, $answers[$incanswers])) {
                                    $buildedTask = $this->multipleAnswerBuild($answers[$incanswers], $buildedTask);
                                } else
                                    $buildedTask = $buildedTask . "<i>" . $answers[$incanswers][0] . "</i> ";

                                if (isset($clues)) {
                                    if (isset($clues[$key])) {
                                        if (isset($clues[$key][$incClues])) {
                                            $buildedTask = $buildedTask . "{" . $clues[$key][$incClues] . "} ";
                                        }
                                    }
                                }
                                $incClues++;
                                $incanswers++;
                                break;
                            case ('@.'):
                                if (array_key_exists(1, $answers[$incanswers])) {
                                    $buildedTask = $this->multipleAnswerBuild($answers[$incanswers], $buildedTask);
                                } else {
                                    if (isset($clues)) {
                                        if (isset($clues[$key])) {
                                            if (isset($clues[$key][$incClues])) {
                                                $buildedTask = $buildedTask . "<i>" . $answers[$incanswers][0] . "</i> ";
                                                $buildedTask = $buildedTask . "{" . $clues[$key][$incClues] . "}. ";
                                            } else {
                                                $buildedTask = $buildedTask . "<i>" . $answers[$incanswers][0] . "</i>. ";
                                            }
                                        } else {
                                            $buildedTask = $buildedTask . "<i>" . $answers[$incanswers][0] . "</i>. ";
                                        }
                                    } else {
                                        $buildedTask = $buildedTask . "<i>" . $answers[$incanswers][0] . "</i>. ";
                                    }
                                }

                                $incClues++;
                                $incanswers++;
                                break;
                            case ('@?'):
                                if (array_key_exists(1, $answers[$incanswers])) {
                                    $buildedTask = $this->multipleAnswerBuild($answers[$incanswers], $buildedTask);
                                } else
                                    $buildedTask = $buildedTask . "<i>" . $answers[$incanswers][0] . "</i>? ";
                                if (isset($clues)) {
                                    if (isset($clues[$key])) {
                                        if (isset($clues[$key][$incClues])) {
                                            $buildedTask = $buildedTask . "{" . $clues[$key][$incClues] . "} ";
                                        }
                                    }
                                }
                                $incClues++;
                                $incanswers++;
                                break;
                            case ('@,'):
                                if (array_key_exists(1, $answers[$incanswers])) {
                                    $buildedTask = $this->multipleAnswerBuild($answers[$incanswers], $buildedTask);
                                } else
                                    $buildedTask = $buildedTask . "<i>" . $answers[$incanswers][0] . "</i>, ";
                                if (isset($clues)) {
                                    if (isset($clues[$key])) {
                                        if (isset($clues[$key][$incClues])) {
                                            $buildedTask = $buildedTask . "{" . $clues[$key][$incClues] . "} ";
                                        }
                                    }
                                }
                                $incClues++;
                                $incanswers++;
                                break;
                            case ('@!'):
                                if (array_key_exists(1, $answers[$incanswers])) {
                                    $buildedTask = $this->multipleAnswerBuild($answers[$incanswers], $buildedTask);
                                } else
                                    $buildedTask = $buildedTask . "<i>" . $answers[$incanswers][0] . "</i>! ";
                                if (isset($clues)) {
                                    if (isset($clues[$key])) {
                                        if (isset($clues[$key][$incClues])) {
                                            $buildedTask = $buildedTask . "{" . $clues[$key][$incClues] . "} ";
                                        }
                                    }
                                }
                                $incClues++;
                                $incanswers++;
                                break;
                            case ('$'):
                                if (isset($clues)) {
                                    if (isset($clues[$key])) {
                                        if (isset($clues[$key][$incClues])) {
                                            $buildedTask = $buildedTask . "{" . $clues[$key][$incClues] . "} ";
                                        }
                                    }
                                }
                                break;
                            default:
                                $buildedTask = $buildedTask . $word . " ";
                                break;
                        }
                    }

                }
                $newTask[] = $buildedTask;
            }
        }

        return $newTask;
    }

    private
    function cluesWithoutBraces($clues): array
    {
        $arrayWithCluesWithoutBraces = [];
        if (isset($clues)) {
            foreach ($clues as $arr) {
                $levels = null;
                if (isset($arr)) {
                    foreach ($arr as $item) {
                        if (isset($item)) {
                            $levels [] = Str::replace(['{', '}'], '', $item);
                        }
                    }
                }
                $arrayWithCluesWithoutBraces[] = $levels;
            }
        }

        return $arrayWithCluesWithoutBraces;
    }

    public
    function theoriesGet()
    {
        $theories = Theory::get(['id', 'name'])->toArray();
        $theory = [];
        $theory['null'] = null;
        foreach ($theories as $item) {
            $theory[$item['id']] = $item['name'];
        }
        return $theory;
    }

    public
    function gameTypeGet()
    {
        $gameTypes = GameType::where('status', 1)->get(['id', 'name'])->toArray();
        $types = [];

        foreach ($gameTypes as $item) {
            $types[$item['id']] = $item['name'];
        }
        return $types;
    }

    public
    function makeASentence()
    {
        return 'make a sentence';
    }

    public
    function typeWords()
    {
        return 'type words';
    }

    public
    function typeAnswer()
    {
        return 'type answer';
    }

    public
    function insertWords()
    {
        return 'insert words';
    }

    public
    function erudite()
    {
        return 'erudite';
    }

    public
    function millionaire()
    {
        return 'millionaire';
    }

    public
    function linkWords()
    {
        return 'link words';
    }

    private function multipleAnswerBuild($array, $buildedTask)
    {
        $last = count($array);
        $correctAns = null;
        foreach ($array as $key => $ans) {
            if ($key == $last - 1) {
                $correctAns .= $ans;
            } else
                $correctAns .= $ans . "/";
        }
        $buildedTask = $buildedTask . "<i>" . $correctAns . "</i>" . " ";

        return $buildedTask;
    }
}
