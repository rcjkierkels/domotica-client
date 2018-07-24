<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'client:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check git repository for updates';

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
     * @return mixed
     */
    public function handle()
    {
        exec('bash '.app_path('../bin/git/status'), $result);

        exec('git pull origin master');

        die(var_dump($result));
    }
}
