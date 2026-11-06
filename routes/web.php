<?php

use App\Http\Controllers\AnimationController;
use App\Http\Controllers\api\AdventuresController;
use App\Http\Controllers\api\ChapterApiController;
use App\Http\Controllers\api\CharacterApiController;
use App\Http\Controllers\api\DialogueApiController;
use App\Http\Controllers\api\EpisodeApiController;
use App\Http\Controllers\api\FinalQuizController;
use App\Http\Controllers\api\GameApiController;
use App\Http\Controllers\api\IntroApiController;
use App\Http\Controllers\api\LanguageApiController;

//use App\Http\Controllers\api\PracticeApiController;
use App\Http\Controllers\api\MusicApiController;
use App\Http\Controllers\api\SeasonApiController;
use App\Http\Controllers\api\StatusPngPostController;
use App\Http\Controllers\api\SubChapterApiController;
use App\Http\Controllers\api\TextApiController;
use App\Http\Controllers\api\TypefastController;
use App\Http\Controllers\api\V2AdventuresController;
use App\Http\Controllers\api\V2CharacterApiController;
use App\Http\Controllers\api\V2LanguageApiController;
use App\Http\Controllers\api\V2SceneApiController;
use App\Http\Controllers\api\V2TypeFastController;
use App\Http\Controllers\api\VersionController;
use App\Http\Controllers\api\WordApiController;
use App\Http\Controllers\ArcadeGamesController;
use App\Http\Controllers\ArcadePagesController;
use App\Http\Controllers\ArcadeThemesController;
use App\Http\Controllers\ArcadeWordsController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BackgroundController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\DialogueController;
use App\Http\Controllers\DialoguePagesController;
use App\Http\Controllers\EmotionController;
use App\Http\Controllers\EpisodeController;
use App\Http\Controllers\FileAudioController;
use App\Http\Controllers\FileTextController;
use App\Http\Controllers\FileVideoController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GameDescriptionController;
use App\Http\Controllers\GameTemplatesController;

//use App\Http\Controllers\GameTypeController;

//use App\Http\Controllers\IntroductionController;
use App\Http\Controllers\IntroductionController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\MotivationController;
use App\Http\Controllers\PersonageController;
use App\Http\Controllers\PracticeController;
use App\Http\Controllers\PracticePagesController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\SeasonController;
use App\Http\Controllers\Services\HostChecker;

//use App\Http\Controllers\Services\SceneComponents\ComponentsController;
use App\Http\Controllers\SubChapterController;
use App\Http\Controllers\SubtitleController;
use App\Http\Controllers\TextController;
use App\Http\Controllers\TheoryController;
use App\Http\Controllers\TheoryPagesController;
use App\Http\Controllers\UserController;

//use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\WordController;
use App\Http\Controllers\WordDragController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

require_once __DIR__ . '/auth.php';

Route::get('/api/raccoon/version', function () {
    return config('values.version') . " ";
});

Route::group(['middleware' => ['lguest', 'doNotCacheResponse']], function () {

    Route::group(['middleware' => ['admin']], function () {

        Route::get('/admin/cash/infos', [\App\Http\Controllers\Services\SceneComponents\ComponentsController::class, 'infoData'])->name('infos-data');

//    Users

        Route::get('/admin/user', [UserController::class, 'index'])->name('user-index');
        Route::post('/admin/user/delete/{id}', [UserController::class, 'destroy'])->name('user-destroy');
        Route::get('/admin/user/edit/{id}', [UserController::class, 'edit'])->name('user-edit');
        Route::post('/admin/user/update/{id}', [UserController::class, 'update'])->name('user-update');

//        Route::prefix('admin/components')->group(function () {
//            Route::get('/info', [ComponentsController::class, 'infoData'])->name('components-info');
//        });
//
        Route::get('/admin/reports', function () {
            return view('admin.users.reports');
        })->name('reports');


        Route::get('/admin/dmitrii/language/seeder', function () {
            if (Auth::user()->email == 'dimaterticinii@gmail.com') {
                Artisan::call('db:seed', ['--class' => 'LanguagesSeeder']);
                return redirect(route('season-index'));
            } else {
                return view('errors.404');
            }

        })->name('lang-seeder');

        Route::get('/admin/dmitrii/cash/clear', function () {
            Artisan::call('responsecache:clear');
            return redirect(route('season-index'));
        })->name('cash-clear');

        Route::get('/admin/dmitrii/roles/seeder', function () {
            if (Auth::user()->email == 'dimaterticinii@gmail.com') {
                Artisan::call('db:seed', ['--class' => 'RolesSeeder']);
                return redirect(route('season-index'));
            } else {
                return view('errors.404');
            }

        })->name('role-seeder');

    });


    Route::get('/', function () {
        return redirect(\route('season-index'));
    });

    Route::get('/permissions', function () {
        return view('auth.permissions');
    })->name('ask-permissions');

//    Route::get('/admin/dashboard', function () {
//        return view('layouts.app');
//    })->name("home");

    Route::prefix('admin')->group(function () {
        Route::group(['middleware' => ['user']], function () {

            // =====  arcade storage ======
            Route::prefix('arcade-storage')->group(function () {
                Route::get('/', [ArcadeThemesController::class, 'index'])->name('arcade-storage');
                Route::get('/create', [ArcadeThemesController::class, 'create'])->name('arcade-storage-create');
                Route::post('/store', [ArcadeThemesController::class, 'store'])->name('arcade-storage-store');
                Route::get('/edit/{id}', [ArcadeThemesController::class, 'edit'])->name('arcade-storage-edit');
                Route::post('/update/{id}', [ArcadeThemesController::class, 'update'])->name('arcade-storage-update');
                Route::post('/delete/{id}', [ArcadeThemesController::class, 'destroy'])->name('arcade-storage-destroy');

                Route::get('/theme/{id}', [ArcadeWordsController::class, 'index'])->name('arcade-storage-theme');
                Route::get('/theme-word-create/{id}', [ArcadeWordsController::class, 'create'])->name('arcade-theme-word-create');
                Route::post('/theme-word-store/{theme}', [ArcadeWordsController::class, 'store'])->name('arcade-theme-word-store');
                Route::get('/theme-word-edit/{theme}/{id}', [ArcadeWordsController::class, 'edit'])->name('arcade-theme-word-edit');
                Route::post('/theme-word-update/{theme}/{id}', [ArcadeWordsController::class, 'update'])->name('arcade-theme-word-update');
                Route::post('/theme-word-delete/{id}', [ArcadeWordsController::class, 'destroy'])->name('arcade-theme-word-destroy');
                Route::post('/theme-word-import', [ArcadeWordsController::class, 'import'])->name('arcade-theme-word-import');

            });

            Route::prefix('arcade-games')->group(function () {
                Route::get('/', [ArcadeGamesController::class, 'index'])->name('arcade-games');

                // ===== arcade games create
                Route::get('/create', [ArcadeGamesController::class, 'create'])->name('arcade-games-create');
                Route::post('/store', [ArcadeGamesController::class, 'store'])->name('arcade-games-store');
                Route::get('/edit/{id}', [ArcadeGamesController::class, 'edit'])->name('arcade-games-edit');
                Route::post('/update/{id}', [ArcadeGamesController::class, 'update'])->name('arcade-games-update');
                Route::post('/delete/{id}', [ArcadeGamesController::class, 'destroy'])->name('arcade-games-destroy');

                // ===== into arcade games list

                Route::get('/page/create-and-edit/{type?}/{id?}', [ArcadePagesController::class, 'create'])->name('arcade-page-create');
                Route::post('/page/store/{id?}', [ArcadePagesController::class, 'store'])->name('arcade-page-store');
                Route::post('/page/delete/{id?}', [ArcadePagesController::class, 'destroy'])->name('arcade-page-delete');


                // ===== Intro create
//                Route::get('/list/intro-create', function () {
//                    return view('admin.arcade-games.includes.intro');
//                })->name('arcade-game-intro');
//
//                // ===== Intro edit
//                Route::get('/list/intro-edit', function () {
//                    return view('admin.arcade-games.includes.intro-edit');
//                })->name('arcade-game-intro-edit');
//
//                // ===== Notification create
//                Route::get('/list/notification', function () {
//                    return view('admin.arcade-games.includes.notification');
//                })->name('arcade-game-notification');
//
//                // ===== Notification edit
//                Route::get('/list/notification-edit', function () {
//                    return view('admin.arcade-games.includes.notification-edit');
//                })->name('arcade-game-notification-edit');
            });
//
//
//            // ===== EndGame create
//            Route::get('/arcade-games/list/end-game', function () {
//                return view('admin.arcade-games.includes.end-game');
//            })->name('arcade-game-end-game');
//
//            // ===== EndGame edit
//            Route::get('/arcade-games/list/end-game-edit', function () {
//                return view('admin.arcade-games.includes.end-game-edit');
//            })->name('arcade-game-end-game-edit');
//
//            // ===== Choices arcade games list
//            Route::get('/arcade-games/choices-list', function () {
//                return view('admin.arcade-games.arcade-games-choices-list');
//            })->name('arcade-games-choices-list');
//
//            // ===== Choices create
//            Route::get('/arcade-games/choices-list/choice', function () {
//                return view('admin.arcade-games.includes.choice');
//            })->name('arcade-games-choices');
//
//            // ===== Choices edit
//            Route::get('/arcade-games/choices-list/choice-edit', function () {
//                return view('admin.arcade-games.includes.choice-edit');
//            })->name('arcade-games-choices-edit');
//
//            // ===== Sub-choice create
//            Route::get('/arcade-games/choices-list/sub-choice', function () {
//                return view('admin.arcade-games.includes.sub-choice');
//            })->name('arcade-games-sub-choice');
//
//            // ===== Sub-choice edit
//            Route::get('/arcade-games/choices-list/sub-choice-edit', function () {
//                return view('admin.arcade-games.includes.sub-choice-edit');
//            })->name('arcade-games-sub-choice-edit');
//
//            // ===== WinAlert create
//            Route::get('/arcade-games/list/win-alert', function () {
//                return view('admin.arcade-games.includes.win-alert');
//            })->name('arcade-game-win-alert');
//
//            // ===== WinAlert edit
//            Route::get('/arcade-games/list/win-alert-edit', function () {
//                return view('admin.arcade-games.includes.win-alert-edit');
//            })->name('arcade-game-win-alert-edit');


            Route::get('/list/{id}', [ArcadePagesController::class, 'index'])->name('arcade-games-list');


            //      Python
            Route::get('/admin/users-data', function () {
                $host = "production";
                if (Str::contains(config('values.host'), 'langly-postgresql-stage')) {
                    $host = 'stage';
                }
                if ($host == 'stage' && Auth::check() && Auth::user()->hasRole(['admin', 'qa', 'project', 'developer'])) {
                    return view('admin.users.users-data');
                } elseif (Auth::check() && Auth::user()->hasRole(['admin', 'qa', 'project'])) {
                    return view('admin.users.users-data');
                } else return redirect()->back();
            })->name('user-data');

            Route::get('/admin/status', function () {
                $host = "production";
                if (Str::contains(config('values.host'), 'langly-postgresql-stage')) {
                    $host = 'stage';
                }
                if ($host == 'stage' && Auth::check() && Auth::user()->hasRole(['admin', 'qa', 'project', 'developer'])) {
                    return view('admin.status.index');
                } elseif (Auth::check() && Auth::user()->hasRole(['admin', 'qa', 'project'])) {
                    return view('admin.status.index');
                } else return redirect()->back();
            })->name('status');

            Route::get('/admin/users-energy', function () {
                $hostCheck = new HostChecker();
                $host = $hostCheck->hostCheck(config('values.host'));
                if ($host == 'stage' && Auth::check() && Auth::user()->hasRole(['admin', 'qa', 'project', 'developer'])) {
                    return view('admin.users.users-energy');
                } else {
                    if (Auth::check() && Auth::user()->hasRole(['admin', 'qa', 'project'])) {
                        return view('admin.users.users-energy');
                    }
                }
            })->name('user-energy');

            Route::get('/admin/exp', function () {
                $hostCheck = new HostChecker();
                $host = $hostCheck->hostCheck(config('values.host'));
                if ($host == 'stage' && Auth::check() && Auth::user()->hasRole(['admin', 'qa', 'project', 'developer'])) {
                    return view('admin.users.exp');
                } else {
                    if (Auth::check() && Auth::user()->hasRole(['admin', 'qa', 'project'])) {
                        return view('admin.users.exp');
                    }
                }
            })->name('exp');

            Route::get('/admin/status-settings', function () {
                $hostCheck = new HostChecker();
                $host = $hostCheck->hostCheck(config('values.host'));
                if ($host == 'stage' && Auth::check() && Auth::user()->hasRole(['admin', 'qa', 'project', 'developer'])) {
                    return view('admin.users.status-settings');
                } else {
                    if (Auth::check() && Auth::user()->hasRole(['admin', 'qa', 'project'])) {
                        return view('admin.users.status-settings');
                    }
                }
            })->name('status-settings');

            Route::get('/admin/arcade-vip', function () {
                $hostCheck = new HostChecker();
                $host = $hostCheck->hostCheck(config('values.host'));
                if ($host == 'stage' && Auth::check() && Auth::user()->hasRole(['admin', 'qa', 'project', 'developer'])) {
                    return view('admin.users.arcade-vip');
                } else {
                    if (Auth::check() && Auth::user()->hasRole(['admin', 'qa', 'project'])) {
                        return view('admin.users.arcade-vip');
                    }
                }
            })->name('arcade-vip');
            //      Python

            Route::prefix('quiz')->group(function () {
                Route::post('/delete/{id}', [QuizController::class, 'destroy'])->name('quiz-destroy');

            });

            Route::post('/season/delete/{id}', [SeasonController::class, 'destroy'])->name('season-destroy');
            Route::post('/episode/delete/{id}', [EpisodeController::class, 'destroy'])->name('episode-destroy');
            Route::post('/chapter/delete/{id}', [ChapterController::class, 'destroy'])->name('chapter-destroy');
            Route::post('/sub-chapter/delete/{id}', [SubChapterController::class, 'destroy'])->name('sub-chapter-destroy');
            Route::post('/dialogue/delete/{id}', [DialogueController::class, 'destroy'])->name('dialogue-destroy');
            Route::post('/dialogue/page/delete/{id}', [DialoguePagesController::class, 'destroy'])->name('dialogue-page-delete');


            //      Languages

            Route::prefix('main')->group(function () {
                Route::prefix('menu')->group(function () {
                    Route::post('/language/delete/{id}', [LanguageController::class, 'mainMenuDestroy'])->name('main-menu-language-destroy');
                    Route::get('/language/create/modal', [LanguageController::class, 'mainMenuCreate'])->name('main-menu-language-create');
                    Route::post('/language/store/modal', [LanguageController::class, 'mainMenuStore'])->name('main-menu-language-store');
                    Route::post('/language/displayed/{id}/{confirm?}', [LanguageController::class, 'displayed'])->name('is-displayed-to-user');
                    Route::post('/language/web/displayed/{id}/{confirm?}', [LanguageController::class, 'displayedWeb'])->name('is-displayed-to-user-web');
                    Route::post('/language/web/not/displayed/{id}', [LanguageController::class, 'notDisplayedWeb'])->name('is-not-displayed-to-user-web');
                    Route::post('/language/not/displayed/{id}', [LanguageController::class, 'notDisplayed'])->name('is-not-displayed-to-user');
                });
            });

            Route::prefix('language')->group(function () {
                Route::post('/delete/{id}', [LanguageController::class, 'destroy'])->name('language-destroy');
                Route::get('/edit/modal/{id}', [LanguageController::class, 'edit'])->name('language-edit');
                Route::post('/update/{id}', [LanguageController::class, 'updateLang'])->name('language-update');
                Route::get('/create/modal', [LanguageController::class, 'create'])->name('language-create');
                Route::post('/store', [LanguageController::class, 'store'])->name('language-store');
            });
        });
//      Storage

        Route::prefix('main')->group(function () {
            Route::prefix('menu')->group(function () {
                Route::get('/language/edit/modal/{id}', [LanguageController::class, 'mainMenuEdit'])->name('main-menu-language-edit');
                Route::post('/language/update/{id}', [LanguageController::class, 'update'])->name('main-menu-language-update');
            });
        });

        Route::prefix('files')->group(function () {
            Route::prefix('text')->group(function () {
                Route::get('/create', [FileTextController::class, 'create'])->name('file-text-create');
                Route::get('/edit/{id}', [FileTextController::class, 'edit'])->name('file-text-edit');
                Route::post('/store', [FileTextController::class, 'store'])->name('file-text-store');
                Route::post('/update/{id}', [FileTextController::class, 'update'])->name('file-text-update');
                Route::post('/delete/{id}', [FileTextController::class, 'destroy'])->name('file-text-destroy');
            });
            Route::prefix('audio')->group(function () {
                Route::get('/get/{name}', [FileAudioController::class, 'getContent'])->name('file-audio-get');
                Route::get('/create', [FileAudioController::class, 'create'])->name('file-audio-create');
                Route::get('/edit/{id}', [FileAudioController::class, 'edit'])->name('file-audio-edit');
                Route::post('/store', [FileAudioController::class, 'store'])->name('file-audio-store');
                Route::post('/update/{id}', [FileAudioController::class, 'update'])->name('file-audio-update');
                Route::post('/delete/{id}', [FileAudioController::class, 'destroy'])->name('file-audio-destroy');
            });
            Route::prefix('video')->group(function () {
                Route::get('/get/{name}', [FileVideoController::class, 'getContent'])->name('file-video-get');
                Route::get('/create', [FileVideoController::class, 'create'])->name('file-video-create');
                Route::get('/edit/{id}', [FileVideoController::class, 'edit'])->name('file-video-edit');
                Route::post('/store', [FileVideoController::class, 'store'])->name('file-video-store');
                Route::post('/update/{id}', [FileVideoController::class, 'update'])->name('file-video-update');
                Route::post('/delete/{id}', [FileVideoController::class, 'destroy'])->name('file-video-destroy');
            });
        });


//      Backgrounds

        Route::prefix('background')->group(function () {
            Route::post('/delete/{id}', [BackgroundController::class, 'destroy'])->name('background-destroy');
            Route::get('/edit/{id}', [BackgroundController::class, 'edit'])->name('background-edit');
            Route::post('/update/{id}', [BackgroundController::class, 'update'])->name('background-update');
            Route::get('/create', [BackgroundController::class, 'create'])->name('background-create');
            Route::post('/store', [BackgroundController::class, 'store'])->name('background-store');
            Route::any('/temp/upload', [BackgroundController::class, 'tempUpload'])->name('background-temp-upload');
            Route::delete('/temp/upload/revert', [BackgroundController::class, 'tempUploadDelete'])->name('background-temp-upload');
            Route::get('/temp/upload/load', [BackgroundController::class, 'load'])->name('background-load');

        });
        //      Animation

        Route::prefix('animation')->group(function () {
            Route::post('/delete/{id}', [AnimationController::class, 'destroy'])->name('animation-destroy');
            Route::post('/update/{id}', [AnimationController::class, 'update'])->name('animation-update');
            Route::post('/store', [AnimationController::class, 'store'])->name('animation-store');
            Route::any('/temp/upload', [AnimationController::class, 'tempUpload'])->name('animation-temp-upload');
            Route::delete('/temp/upload/revert', [AnimationController::class, 'tempUploadDelete'])->name('animation-temp-upload');
            Route::get('/temp/upload/load', [AnimationController::class, 'load'])->name('animation-load');

        });

//      Quiz

        Route::prefix('quiz')->group(function () {
            Route::get('/get/{name}', [QuizController::class, 'getContent'])->name('quiz-get');
            Route::get('/edit/{id}', [QuizController::class, 'edit'])->name('quiz-edit');
            Route::post('/update/{id}', [QuizController::class, 'update'])->name('quiz-update');
            Route::get('/create', [QuizController::class, 'create'])->name('quiz-create');
            Route::post('/store', [QuizController::class, 'store'])->name('quiz-store');
            Route::any('/temp/upload', [QuizController::class, 'tempUpload'])->name('quiz-temp-upload');
            Route::delete('/temp/upload/revert', [QuizController::class, 'tempUploadDelete'])->name('quiz-temp-upload');
            Route::get('/temp/upload/load', [QuizController::class, 'load'])->name('quiz-load');

        });

//      Emotions

        Route::prefix('emotion')->group(function () {
            Route::post('/delete/{id}', [EmotionController::class, 'destroy'])->name('emotion-destroy');
            Route::get('/edit/{id}', [EmotionController::class, 'edit'])->name('emotion-edit');
            Route::post('/update/{id}', [EmotionController::class, 'update'])->name('emotion-update');
            Route::get('/create', [EmotionController::class, 'create'])->name('emotion-create');
            Route::post('/store', [EmotionController::class, 'store'])->name('emotion-store');
            Route::any('/temp/upload', [EmotionController::class, 'tempUpload'])->name('emotion-temp-upload');
            Route::delete('/temp/upload/revert', [EmotionController::class, 'tempUploadDelete'])->name('emotion-temp-upload');
        });

//      Words

        Route::post('/word/delete/{id}', [WordController::class, 'destroy'])->name('word-destroy');
        Route::get('/word/edit/{id}', [WordController::class, 'edit'])->name('word-edit');
        Route::post('/word/update/{id}', [WordController::class, 'update'])->name('word-update');
        Route::get('/word/create', [WordController::class, 'create'])->name('word-create');
        Route::post('/word/store', [WordController::class, 'store'])->name('word-store');
        Route::post('/word/get-data', [WordController::class, 'getDataFromOxford'])->name('get-oxford-data');
        Route::post('/word/search', [WordController::class, 'trySearch'])->name('word-search');
        Route::post('/words/search', [WordController::class, 'trySearchW'])->name('wordw-search');

//      Theory

        Route::post('/theory/delete/{id}', [TheoryController::class, 'destroy'])->name('theory-destroy');
        Route::post('/theory/topic-grammar/delete/{id}', [TheoryController::class, 'tGDestroy'])->name('theory-topic-grammar-destroy');
        Route::get('/theory/edit/{id}', [TheoryController::class, 'edit'])->name('theory-edit');
        Route::post('/theory/update/{id}', [TheoryController::class, 'update'])->name('theory-update');
        Route::get('/theory/create/{type?}', [TheoryController::class, 'create'])->name('theory-create');
        Route::post('/theory/store', [TheoryController::class, 'store'])->name('theory-store');

//      Learning

        Route::post('/learning/delete/{id}', [LearningController::class, 'destroy'])->name('learning-destroy');
        Route::get('/learning/edit/{id}', [LearningController::class, 'edit'])->name('learning-edit');
        Route::get('/learning/practice/edit/{id}/{learningId}/{practiceId}', [LearningController::class, 'practicePageEdit'])->name('learning-practice-edit');
        Route::post('/learning/update/{id}', [LearningController::class, 'update'])->name('learning-update');
        Route::post('/learning/practice/update/{id}/{learningId}/{practiceId}', [LearningController::class, 'practicePageUpdate'])->name('learning-practice-update');
        Route::get('/learning/create', [LearningController::class, 'create'])->name('learning-create');
        Route::post('/learning/store', [LearningController::class, 'store'])->name('learning-store');
        Route::get('/practice/link/edit/{learning}/{id}', [LearningController::class, 'practiceLinkGet'])->name('learning-link-get');
        Route::post('/practice/link/update/{learning}/{id}', [LearningController::class, 'practiceLinkPost'])->name('learning-link-post');

//      Practice

        Route::post('/practice/delete/{id}/{val}', [PracticeController::class, 'destroy'])->name('practice-destroy');
        Route::get('/practice/modal/delete/{id}', [PracticeController::class, 'deleteModal'])->name('practice-modal');
        Route::get('/practice/edit/{id}', [PracticeController::class, 'edit'])->name('practice-edit');
        Route::post('/practice/update/{id}', [PracticeController::class, 'update'])->name('practice-update');
        Route::get('/practice/create', [PracticeController::class, 'create'])->name('practice-create');
        Route::post('/practice/store', [PracticeController::class, 'store'])->name('practice-store');

//      Practice-Pages

        Route::post('/practice/pages/delete/{id}/{practiceId}', [PracticePagesController::class, 'destroy'])->name('practice-destroy');
        Route::get('/practice/pages/create/{practiceId}', [PracticePagesController::class, 'create'])->name('practice-pages-create');
        Route::post('/practice/pages/store/{practiceId}', [PracticePagesController::class, 'store'])->name('practice-pages-store');

//      Theory-Words

        Route::get('/theory/words/create/{theoryId}', [TheoryController::class, 'wordCreate'])->name('theory-words-create');
        Route::post('/theory/words/store/{theoryId}', [TheoryController::class, 'wordStore'])->name('theory-words-edit');
        Route::get('/theory/words/edit/{id}/{theoryId}', [TheoryController::class, 'wordEdit'])->name('theory-words-edit');
        Route::post('/theory/words/delete/{id}/{theoryId}', [TheoryController::class, 'wordDestroy'])->name('theory-words-destroy');

//      Theory-Pages

        Route::post('/theory/pages/create/localisation/{theoryId}/{theoryPageStackId}/{language?}', [TheoryPagesController::class, 'createLocalization'])->name('theory-pages-create-localisation');
        Route::get('/theory/pages/create/{theoryId}', [TheoryPagesController::class, 'create'])->name('theory-pages-create');
        Route::get('/theory/pages/edit/{theoryId}/{id}', [TheoryPagesController::class, 'edit'])->name('theory-pages-edit');
        Route::post('/theory/pages/store/{theoryId}', [TheoryPagesController::class, 'store'])->name('theory-pages-store');
        Route::post('/theory/pages/update/{theoryId}', [TheoryPagesController::class, 'update'])->name('theory-pages-update');
        Route::post('/theory/pages/delete/{id}', [TheoryPagesController::class, 'destroy'])->name('theory-pages-destroy');


//      Dialogues
//
//        Route::post('/admin/dialogue/delete/{id}', [DialogueController::class, 'destroy'])->name('story-destroy');
//        Route::get('/admin/story/edit/{id}/{season_id}/{episode_id}/{chapter_id}/{sub_chapter_id}', [DialogueController::class, 'edit'])->name('story-edit');
//        Route::post('/admin/story/update/{id}/{season_id}/{episode_id}/{chapter_id}/{sub_chapter_id}', [DialogueController::class, 'update'])->name('story-update');
//        Route::get('/admin/story/create', [DialogueController::class, 'create'])->name('story-create');
//        Route::post('/admin/story/store/{season_id}/{episode_id}/{chapter_id}/{sub_chapter_id}', [DialogueController::class, 'store'])->name('story-store');
        Route::post('/story/emotions/get', [DialogueController::class, 'getEmotion'])->name('emotion-get');
//        Route::post('/admin/story/order/{subChapter_id}', [DialogueController::class, 'setSubChapterOrder'])->name('game-order');


//      Personages

        Route::post('/personage/delete/{id}', [PersonageController::class, 'destroy'])->name('personage-destroy');
        Route::get('/personage/edit/{id}', [PersonageController::class, 'edit'])->name('personage-edit');
        Route::post('/personage/update/{id}', [PersonageController::class, 'update'])->name('personage-update');
        Route::get('/personage/create', [PersonageController::class, 'create'])->name('personage-create');
        Route::post('/personage/store', [PersonageController::class, 'store'])->name('personage-store');

//      Game Description

        Route::post('/game-description/delete/{id}', [GameDescriptionController::class, 'destroy'])->name('game-description-destroy');
        Route::get('/game-description/edit/{id}', [GameDescriptionController::class, 'edit'])->name('game-description-edit');
        Route::post('/game-description/update/{id}', [GameDescriptionController::class, 'update'])->name('game-description-update');
        Route::get('/game-description/create', [GameDescriptionController::class, 'create'])->name('game-description-create');
        Route::post('/game-description/store', [GameDescriptionController::class, 'store'])->name('game-description-store');

//      Game Type

        Route::post('/theory/delete/{id}', [TheoryController::class, 'destroy'])->name('theory-destroy');
        Route::get('/theory/edit/{id}', [TheoryController::class, 'edit'])->name('theory-edit');
        Route::post('/theory/update/{id}', [TheoryController::class, 'update'])->name('theory-update');
        Route::get('/theory/create', [TheoryController::class, 'create']);
        Route::post('/theory/store', [TheoryController::class, 'store'])->name('theory-store');

//      Game Templates

        Route::get("/game/templates/create", [GameTemplatesController::class, 'create'])->name('game-templates-create');
        Route::get("/game/templates/delete/modal/{id}", [GameTemplatesController::class, 'deleteModal'])->name('game-templates-delete-modal');
        Route::get("/game/templates/preview/{id}", [GameTemplatesController::class, 'show'])->name('game-templates-preview');
        Route::post("/game/templates/store", [GameTemplatesController::class, 'store'])->name('game-templates-store');
        Route::post("/game/templates/delete/{id}/{val}", [GameTemplatesController::class, 'destroy'])->name('game-templates-destroy');

//      Texts

        Route::prefix('text')->group(function () {
            Route::post('/delete/{id}', [TextController::class, 'destroy'])->name('text-destroy');
            Route::get('/edit/{id}', [TextController::class, 'edit'])->name('text-edit');
            Route::post('/update/{id}', [TextController::class, 'update'])->name('text-update');
            Route::get('/create', [TextController::class, 'create'])->name('text-create');
            Route::post('/store', [TextController::class, 'store'])->name('text-store');
            Route::post('/search/results', [TextController::class, 'textSearch'])->name('text-search');
            Route::get('/export', [TextController::class, 'exportGet'])->name('text-export');
            Route::post('/preview', [TextController::class, 'textPreview'])->name('text-preview');
            Route::post('/import', [TextController::class, 'import'])->name('text-import');
            Route::post('/export/localisation', [TextController::class, 'export'])->name('text-export-localisation');
            Route::get('/download/localisation/{filename}', [TextController::class, 'download'])->name('text-download-localisation');
        });

//      Welcome

        Route::prefix('welcome')->group(function () {
            Route::post('/delete/{id}', [WelcomeController::class, 'destroy'])->name('welcome-destroy');
            Route::get('/edit/{id}', [WelcomeController::class, 'edit'])->name('welcome-edit');
            Route::post('/update/{id}', [WelcomeController::class, 'update'])->name('welcome-update');
            Route::get('/create', [WelcomeController::class, 'create'])->name('welcome-create');
            Route::post('/store', [WelcomeController::class, 'store'])->name('welcome-store');
        });

//      Introduction

        Route::prefix('introduction')->group(function () {
            Route::post('/delete/{id}', [IntroductionController::class, 'destroy'])->name('introduction-destroy');
            Route::get('/edit/{id}', [IntroductionController::class, 'edit'])->name('introduction-edit');
            Route::post('/update/{id}', [IntroductionController::class, 'update'])->name('introduction-update');
            Route::get('/create', [IntroductionController::class, 'create'])->name('introduction-create');
            Route::post('/store', [IntroductionController::class, 'store'])->name('introduction-store');
        });

        //      Motivation

        Route::prefix('motivation')->group(function () {
            Route::post('/delete/{id}', [MotivationController::class, 'destroy'])->name('motivation-destroy');
            Route::get('/edit/{id}', [MotivationController::class, 'edit'])->name('motivation-edit');
            Route::post('/update/{id}', [MotivationController::class, 'update'])->name('motivation-update');
            Route::get('/create', [MotivationController::class, 'create'])->name('motivation-create');
            Route::post('/store', [MotivationController::class, 'store'])->name('motivation-store');
        });

        //      Subtitle

        Route::prefix('subtitle')->group(function () {
            Route::post('/delete/{id}', [SubtitleController::class, 'destroy'])->name('subtitle-destroy');
            Route::get('/edit/{id}', [SubtitleController::class, 'edit'])->name('subtitle-edit');
            Route::post('/update/{id}', [SubtitleController::class, 'update'])->name('subtitle-update');
            Route::get('/create', [SubtitleController::class, 'create'])->name('subtitle-create');
            Route::post('/store', [SubtitleController::class, 'store'])->name('subtitle-store');
        });


        //Welcome

//            Route::get('/welcome/create',[WelcomeController::class,'create'])->name('welcome-create');

        //Introduction

//            Route::get('/introduction/create',[IntroductionController::class,'create'])->name('introduction-create');


//      Seasons
        Route::prefix('season')->group(function () {
            Route::get('/edit/{id}', [SeasonController::class, 'edit'])->name('season-edit');
            Route::post('/update/{id}', [SeasonController::class, 'update'])->name('season-update');
            Route::get('/create', [SeasonController::class, 'create'])->name('season-create');
            Route::post('/store', [SeasonController::class, 'store'])->name('season-store');
            Route::post('/order', [SeasonController::class, 'setSeasonOrder'])->name('season-order');
            Route::get('/modal/{id}', [SeasonController::class, 'modalViewGet'])->name('season-modal');
            Route::post('/lock-unlock/{id}/{isClosed}', [SeasonController::class, 'isDisplayedToUser'])->name('season-lock');
        });

//      Episodes
        Route::prefix('episode')->group(function () {

            Route::get('/edit/{id}/{season_id}', [EpisodeController::class, 'edit'])->name('episode-edit');
            Route::get('/final-quiz/edit/{id}/{season_id}', [EpisodeController::class, 'finalQuizEdit'])->name('episode-final-edit');
            Route::post('/update/{id}/{season_id}', [EpisodeController::class, 'update'])->name('episode-update');
            Route::post('/store/{season_id}', [EpisodeController::class, 'store'])->name('episode-store');
            Route::get('/create/{season_id}', [EpisodeController::class, 'create'])->name('episode-create');
            Route::get('/final-quiz/create/{season_id}', [EpisodeController::class, 'finalQuizCreate'])->name('episode-final-create');
            Route::post('/order/{season_id}', [EpisodeController::class, 'setEpisodeOrder'])->name('episode-order');
            Route::get('/modal/{id}', [EpisodeController::class, 'modalViewGet'])->name('episode-modal');
            Route::post('/lock-unlock/{id}/{isClosed}', [EpisodeController::class, 'isDisplayedToUser'])->name('episode-lock');
            Route::get('/constructor/{id}', function ($id) {
                return view('admin.episodes.constructor')->with(compact('id'));
            })->name('episode-constructor');


        });

//      Chapter
        Route::prefix('chapter')->group(function () {

            Route::get('/edit/{id}/{season_id}/{episode_id}', [ChapterController::class, 'edit'])->name('chapter-edit');
            Route::get('/create/{season_id}/{episode_id}/{type?}', [ChapterController::class, 'create'])->name('chapter-create');
            Route::post('/update/{id}/{season_id}/{episode_id}', [ChapterController::class, 'update'])->name('chapter-update');
            Route::post('/store/{season_id}/{episode_id}', [ChapterController::class, 'store'])->name('chapter-store');
            Route::post('/order/{episode_id}', [ChapterController::class, 'setChapterOrder'])->name('chapter-order');
            Route::get('/modal/{id}', [ChapterController::class, 'modalViewGet'])->name('chapter-modal');
            Route::post('/lock-unlock/{id}/{isClosed}', [ChapterController::class, 'isDisplayedToUser'])->name('chapter-lock');
        });


//      Sub-Chapter
        Route::prefix('sub-chapter')->group(function () {

            Route::get('/edit/{id}/{season_id}/{episode_id}/{chapter_id}', [SubChapterController::class, 'edit'])->name('sub-chapter-edit');
            Route::get('/create/{season_id}/{episode_id}/{chapter_id}', [SubChapterController::class, 'create'])->name('sub-chapter-create');
            Route::post('/update/{id}/{season_id}/{episode_id}/{chapter_id}', [SubChapterController::class, 'update'])->name('sub-chapter-update');
            Route::post('/store/{season_id}/{episode_id}/{chapter_id}', [SubChapterController::class, 'store'])->name('sub-chapter-store');
            Route::post('/order/{chapter_id}', [SubChapterController::class, 'setSubChapterOrder'])->name('sub-chapter-order');
            Route::get('/modal/{id}', [SubChapterController::class, 'modalViewGet'])->name('sub-chapter-modal');
            Route::post('/theory/set', [SubChapterController::class, 'theorySet'])->name('theory-set');
            Route::post('/lock-unlock/{id}/{isClosed}', [SubChapterController::class, 'isDisplayedToUser'])->name('sub-chapter-lock');
        });

//      Dialogue
        Route::prefix('dialogue')->group(function () {

            Route::get('/edit/{id}', [DialogueController::class, 'edit'])->name('dialogue-edit');
            Route::get('/create', [DialogueController::class, 'create'])->name('dialogue-create');
            Route::post('/update/{id}', [DialogueController::class, 'update'])->name('dialogue-update');
            Route::post('/store', [DialogueController::class, 'store'])->name('dialogue-store');


//      Dialogue Pages

            Route::get('/page/create-and-edit/{type?}/{id?}', [DialoguePagesController::class, 'create'])->name('dialogue-page-create');
            Route::post('/page/store/{id?}', [DialoguePagesController::class, 'store'])->name('dialogue-page-store');
            Route::get('/export/{id}', [DialoguePagesController::class, 'exportGet'])->name('dialogue-export');
            Route::post('/export/localisation/{id}', [DialoguePagesController::class, 'export'])->name('dialogue-export-localisation');
            Route::get('/download/localisation/{filename}', [DialoguePagesController::class, 'download'])->name('dialogue-download-localisation');
            Route::post('/import', [DialoguePagesController::class, 'import'])->name('dialogue-import');
        });
        Route::post('/new/dialogue/import/{id}', [DialoguePagesController::class, 'newDialogueImport'])->name('new-dialogue-import');

//      Games

        Route::post('/game/delete/{id}', [GameController::class, 'destroy'])->name('game-destroy');
        Route::post('/game/update/{id}/{practiceId}/{templateId}/{learningId}', [GameController::class, 'update'])->name('game-update');
        Route::post('/game/store/{practiceId}/{templateId}/{learningId}', [GameController::class, 'store'])->name('game-store');
        Route::post('/games/order/{subChapter_id}', [GameController::class, 'setSubChapterOrder'])->name('games-order');
        Route::get('/game/edit/{id}/{practiceId}/{templateId}/{learningId}', [GameController::class, 'edit'])->name('game-edit');
        Route::get('/game/create/{practiceId}/{templateId}/{learningId}', [GameController::class, 'create'])->name('game-create');
        Route::get('/game/create/modal', [GameController::class, 'getUnicForm'])->name('game-unic-form');
        Route::get('/game/edit/modal/{id}', [GameController::class, 'getUnicEditForm'])->name('game-unic-edit-form');
        Route::get('/game/get/{name}', [GameController::class, 'getContent'])->name('game-audio-get');


//      Drag and Drop word

        Route::post('/d-and-d/words/order/{subChapter_id}', [WordDragController::class, 'assignPost']);

//      Languages

        Route::get('/language', [LanguageController::class, 'index'])->name('language-index');
        Route::get('/main/menu/language', [LanguageController::class, 'mainMenuIndex'])->name('main-language-index');

//      Words

        Route::get('/word/{filter?}/{order?}', [WordController::class, 'index'])->name('word-index');

//      Drag and Drop word

        Route::get('/d-and-d/words/{season_id}/{episode_id}/{chapter_id}/{subChapter_id}', [WordDragController::class, 'index'])->name('words-index');
        Route::get('/d-and-d/words/assign/{subChapter_id}', [WordDragController::class, 'assignGet'])->name('words-assigned');

//      Game Description

        Route::get('/game-description', [GameDescriptionController::class, 'index'])->name('game-description-index');

//      Theory

        Route::get('/theory', [TheoryController::class, 'index'])->name('theory-index');

//      Learning

        Route::get('/learning', [LearningController::class, 'index'])->name('learning-index');
        Route::get('/learning/show/{id}', [LearningController::class, 'show'])->name('learning-show');
        Route::get('/learning/practice/{id}/{learningId}', [LearningController::class, 'practicePageIndex'])->name('learning-practice');

//    Theory-Pages

        Route::get('/theory-pages/{id}', [TheoryPagesController::class, 'index'])->name('theory-pages-index');

//      Theory-Words

        Route::get('/theory/words/{id}/{filter?}/{order?}', [TheoryController::class, 'wordIndex'])->name('theory-words-index');

//      Texts

        Route::get('/text', [TextController::class, 'index'])->name('text-index');

//      Welcomes

        Route::get('/welcome', [WelcomeController::class, 'index'])->name('welcome-index');

//      Introduction

        Route::get('/introduction', [IntroductionController::class, 'index'])->name('introduction-index');

//      Motivation

        Route::get('/motivation', [MotivationController::class, 'index'])->name('motivation-index');

        //      Subtitle

        Route::get('/subtitle', [SubtitleController::class, 'index'])->name('subtitle-index');

//      Welcome

//        Route::get('/welcome', [WelcomeController::class, 'index'])->name('welcome-index');

//      Seasons

        Route::get('/season/{filter?}', [SeasonController::class, 'index'])->name('season-index');


//      Episodes

        Route::get('/season/episodes/{season_id}/{filter?}', [EpisodeController::class, 'index'])->name('episode-index');


//      Chapter

        Route::get('/episode/chapters/{season_id}/{episode_id}/{filter?}', [ChapterController::class, 'index'])->name('chapter-index');


//      Sub-Chapter

        Route::get('/episode/sub-chapters/{season_id}/{episode_id}/{chapter_id}/{filter?}', [SubChapterController::class, 'index'])->name('sub-chapter-index');


//      Games

        Route::get('/sub-chapters/games/{learningsId}/{practiceId}/{id}', [GameController::class, 'index'])->name('game-index');

//      Game Templates

        Route::get('/game/templates', [GameTemplatesController::class, 'index'])->name('game-templates-index');

//      Dialogues

        Route::get('/sub-chapters/story/{season_id}/{episode_id}/{chapter_id}/{subChapter_id}', [DialogueController::class, 'index'])->name('story-index');

//      Personage

        Route::get('/personage', [PersonageController::class, 'index'])->name('personage-index');

//      Backgrounds

        Route::get('/background', [BackgroundController::class, 'index'])->name('background-index');

        //      Animation

        Route::get('/animation', [AnimationController::class, 'index'])->name('animation-index');


        Route::get('/quiz', [QuizController::class, 'index'])->name('quiz-index');

//    Emotions

        Route::get('/emotion/{id}', [EmotionController::class, 'index'])->name('emotion-index');

//  Dialogue's-Pages

        Route::get('/dialogue/page/{chapterId}/{parentId?}', [DialoguePagesController::class, 'index'])->name('dialogue-page-index');

//    Dialogues

        Route::get('/dialogue', [DialogueController::class, 'index'])->name('dialogue-index');

//    Practices

        Route::get('/practice', [PracticeController::class, 'index'])->name('practice-index');

//    Practices-Pages

        Route::get('/practice/pages/{practiceId}', [PracticePagesController::class, 'index'])->name('practice-pages-index');

//Storage
        Route::prefix('files')->group(function () {
            Route::get('text', [FileTextController::class, 'index'])->name('file-text-index');
            Route::get('audio', [FileAudioController::class, 'index'])->name('file-audio-index');
            Route::get('video', [FileVideoController::class, 'index'])->name('file-video-index');
        });
    });
});
Route::prefix('admin/animation')->group(function () {
    Route::get('/get/{name}', [AnimationController::class, 'getContent'])->name('animation-get')->middleware('doNotCacheResponse');
});
Route::prefix('admin/dialogue')->group(function () {
    Route::get('/get/{name}', [DialoguePagesController::class, 'getContent'])->name('dialogue-audio-get');

});


//      API


Route::prefix('api')->group(function () {
//    Route::group(['middleware' => ['cash']], function () {
        Route::get('/language', [LanguageApiController::class, 'languageList'])->name('language-api');
        Route::get('/language/gen', [LanguageApiController::class, 'columnGen']);
        Route::post('/text/create', [TextApiController::class, 'create']);
        Route::get('/text', [TextApiController::class, 'textGet'])->name('text-api');
        Route::get('/language/icon/{name}', [LanguageApiController::class, 'langIco']);
        Route::get('/seasons', [SeasonApiController::class, 'allSeasons'])->name('season-api');
        Route::get('/episodes', [EpisodeApiController::class, 'getAllSeasonsEpisodes'])->name('episode-api');
        Route::get('/chapters', [ChapterApiController::class, 'getAllEpisodesChapters'])->name('chapter-api');
        Route::get('/sub-chapters', [SubChapterApiController::class, 'getAllChaptersSubChapters'])->name('sub-chapter-api');
        Route::get('/games/{sub_chapter_id}/{iso}', [GameApiController::class, 'getAllSubChaptersGames'])->name('games-api');
        Route::get('/sub-chapter/entity/{sub_chapter_id}/{iso}', [WordApiController::class, 'getGamesAndWordsOfSubchapterWordEntity'])->name('word-api');
        Route::get('/game/{id}/{iso}', [GameApiController::class, 'getGame'])->name('game-api');
        Route::get('/story/{id}/{iso}/{size?}', [DialogueApiController::class, 'getDialogue'])->name('story-api');
        Route::get('/intro', [IntroApiController::class, 'introGet'])->name('intro-api');
//        ->middleware('doNotCacheResponse');
        Route::get('/characters', [CharacterApiController::class, 'getAllCharacters']);
        Route::get('/typefast', [TypefastController::class, 'typeFast']);
        Route::get('/theories', [\App\Http\Controllers\api\TheoryController::class, 'getTheories']);
        Route::post('/adventures', [AdventuresController::class, 'adventuresCoordinatesPost']);
        Route::get('/v2/seasons', [V2AdventuresController::class, 'allSeasonsV2']);
        Route::post('/status-media/save', [StatusPngPostController::class, 'StatusPngPost']);
        Route::get('/status/get/{name}', [StatusPngPostController::class, 'getContent']);

        Route::get('/sub-chapter/get/{name}', [SubChapterController::class, 'getContent'])->name('sub-chapter-get');
        Route::get('/chapter/get/{name}', [ChapterController::class, 'getContent'])->name('chapter-get');
        Route::get('/episode/get/{name}', [EpisodeController::class, 'getContent'])->name('episode-get');
        Route::get('/episode/additional/get/{name}', [EpisodeController::class, 'getContentAdditional'])->name('episode-get-additional');
        Route::get('/season/get/{name}', [SeasonController::class, 'getContent'])->name('season-get');
        Route::get('/word/get/{name}', [WordController::class, 'getContent'])->name('word-audio-get');
        Route::get('/word/img/get/{name}', [WordController::class, 'getImgContent'])->name('word-img-get');
        Route::get('/main/menu/language/get/{name}', [LanguageController::class, 'getContent'])->name('language-get');
        Route::get('/emotion/get/{name}', [EmotionController::class, 'getContent'])->name('emotion-get');
        Route::get('/background/get/{name}', [BackgroundController::class, 'getContent'])->name('background-get');
        Route::get('/animation/get/{name}', [AnimationController::class, 'getContent'])->name('animation-get');
        Route::get('/dialogue/get/{name}', [DialogueController::class, 'getContent'])->name('dialogue-get');


//    });
//    Route::group(['middleware' => ['jwt.verify']], function () {
//        Route::group(['middleware' => ['cash']], function () {
            Route::get('/v2/text', [TextApiController::class, 'textGet'])->name('text-api');
            Route::get('/v3/seasons', [V2AdventuresController::class, 'allSeasonsV3']);
            Route::get('/{version}/characters', [V2CharacterApiController::class, 'getAllCharacters']);
            Route::get('/{version}/scene', [V2SceneApiController::class, 'sceneGet'])->name('v2dialogue-scene-api');
            Route::get('/{version}/language', [V2LanguageApiController::class, 'languageList']);
            Route::get('/v2/typefast', [TypefastController::class, 'typeFast']);
            Route::get('/arcade-games', [V2TypeFastController::class, 'arcadeGamesGet']);
            Route::get('/music', [MusicApiController::class, 'getContent']);
            Route::get('/versions', [VersionController::class, 'versionApiGet']);
            Route::get('/v2/theories', [\App\Http\Controllers\api\TheoryController::class, 'getTheories']);
            Route::get('/final-quiz', [FinalQuizController::class, 'finalQuizGet']);
//
//        });
//    });
//    Route::get('/test/{id}', [\App\Http\Controllers\Services\Theory::class, 'buildTheoryData']);
});
//->middleware('doNotCacheResponse')

Route::get('/no-admin', function () {
    return view('layouts.app');
});
Route::get('/email-validation/{validate}', [RegisteredUserController::class, 'emailConfirmation'])->name('validation');
Route::get('/email-confirmation', function () {
    return view('auth.verify-email');
})->name('confWait');




