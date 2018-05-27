<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class checkGarageDoor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'client:checkGarageDoor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and log the status of the garage door';

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
        die(var_dump('Hello world'));
    }
}
