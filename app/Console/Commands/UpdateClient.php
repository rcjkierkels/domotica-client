<?php

namespace App\Console\Commands;

use App\Models\Log;
use App\Repositories\ClientRepository;
use Illuminate\Console\Command;

class UpdateClient extends Command
{
    protected $clientRepository;

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
        exec('bash '.app_path('../bin/git/status'), $result);

        if (empty($result) || !isset($result[1])) {
            Log::error('Update', 'Checking', 'Cannot get GIT status. Updating failed');
            return;
        }

        $this->clientRepository->insertOrUpdateClient([
            'last_update_check' => date('Y-m-d H:i:s'),
            'last_commit' => $this->getCurrentCommit(),
        ]);

        switch($result[1])
        {
            case 'up-to-date':
                // nothing to do
                break;
            case 'need-to-pull':
                $this->update();
                break;
            case 'diverged':
            case 'need-to-push':
            Log::error('Update', 'Checking', 'Impossible to update due to local changes or conflicts');
                break;
            default:
                Log::error('Update', 'Checking', 'Unknown GIT status. Updating failed!');
                return;

        }

    }

    protected function getCurrentCommit() : string
    {
        exec('cat .git/refs/heads/master', $commitHash);
        return current($commitHash);
    }

    protected function update()
    {
        exec('git pull origin master');

        exec('composer install');

        $this->clientRepository->insertOrUpdateClient([
            'last_commit' => $this->getCurrentCommit(),
            'last_update_code' => date('Y-m-d H:i:s')
        ]);
    }
}
