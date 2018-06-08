<?php

namespace App\Console\Commands;

use App\Repositories\ClientRepository;
use Illuminate\Console\Command;

class NotifyServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register / Update client information at server side. Show that client is still a live';

    /** @var ClientRepository */
    protected $clientRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->clientRepository->insertOrUpdateClient();
    }
}
