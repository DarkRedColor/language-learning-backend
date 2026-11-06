<?php

namespace App\Console\Commands;

use App\Http\Controllers\Services\Games;
use App\Http\Controllers\Services\Practice;
use App\Models\Game;
use Illuminate\Console\Command;

class GameLinkChanger extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'changer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     *
     */
    public function handle()
    {

        $games = Game::all();

        foreach ($games as $game) {
            if (str_contains($game->mission_en, 'm3d14.langly.ai')) {
                $str = "https://storage.googleapis.com/php_statics_public/game/" . $game->id;
                Game::where('id', $game->id)->update([
                    'mission_en' => json_encode([$str])
                ]);

            }
            if (str_contains($game->theory, 'm3d14.langly.ai')) {
                $str = "https://storage.googleapis.com/php_statics_public/game/additional/" . $game->id;
                Game::where('id', $game->id)->update([
                    'theory' => json_encode([$str])
                ]);

            }
        }

        return 'done';
    }
}
