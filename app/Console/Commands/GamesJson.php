<?php

namespace App\Console\Commands;

use App\Http\Controllers\Services\SceneComponents\ComponentsController;
use Illuminate\Console\Command;

class GamesJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'games:json';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create cash of games';

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
     * @return int
     */
    public function handle()
    {
        $start = now();
        $infos = new ComponentsController();
        echo $infos->gameData();
        $time = $start->diffInSeconds(now());
        echo 'All processed in '.$time;
        return 0;
    }
}
