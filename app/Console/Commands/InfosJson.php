<?php

namespace App\Console\Commands;

use App\Http\Controllers\Services\SceneComponents\ComponentsController;
use Illuminate\Console\Command;

class InfosJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'infos:json';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create cash of infos';

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
        echo $infos->infoData();
        $time = $start->diffInSeconds(now());
        echo 'processed in '.$time;
        return 0;
    }
}
