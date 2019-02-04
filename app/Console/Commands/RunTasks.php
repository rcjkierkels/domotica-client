<?php

namespace App\Console\Commands;

use App\Async\TaskManager;
use App\Models\Log;
use App\Models\Task;
use App\Repositories\ClientRepository;
use App\Repositories\TaskRepository;
use App\Services\CameraService;
use Illuminate\Console\Command;
use Spatie\Async\Pool;
use Throwable;

class RunTasks extends Command
{
    const FOREVER = 1;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'client:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start running client';

    /** @var TaskRepository $taskRepository */
    protected $taskRepository;
    /** @var ClientRepository $clientRepository */
    protected $clientRepository;

    protected $lastUpdateCode;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TaskRepository $taskRepository, ClientRepository $clientRepository)
    {
        $this->taskRepository = $taskRepository;
        $this->clientRepository = $clientRepository;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() : void
    {
        $test = new CameraService();
        $output = $test->takePhoto();
        echo strlen($output);exit;

        $this->resetDeadTasks();

        $pool = Pool::create()
            // The maximum amount of processes which can run simultaneously.
            ->concurrency(20)
            // The maximum amount of time a process may take to finish in seconds.
            ->timeout(60);

        $this->lastUpdateCode = $this->clientRepository->getClient()->last_update_code;

        Log::info('Tasks', 'Watch', 'Client is running. Client is now watching for tasks');

        while( $this->clientCodeHasNotBeenUpdated() )
        {
            $tasks = $this->taskRepository->getQueuedTasks();

            /** @var Task $task */
            foreach($tasks as $task)
            {
                $this->taskRepository->setRunning($task);

                $pool->add(new TaskManager($task))
                     ->then(function($output) use ($task) { $this->finishTask($task, $output); })
                     ->catch(function(Throwable $exception) use ($task) { $this->errorTask($task, $exception); });

            }

            sleep(1);

            $this->clientRepository->insertOrUpdateClient(['last_task_check' => date('Y-m-d H:i:s')]);
        }

        Log::info('Tasks', 'Watch', 'Client stopped running and is no longer watching tasks because of a code update');
    }

    protected function clientCodeHasNotBeenUpdated() : bool
    {
        $currentLastUpdateCode = $this->clientRepository->getClient()->last_update_code;

        if ($currentLastUpdateCode !== $this->lastUpdateCode) {
            $this->lastUpdateCode = $currentLastUpdateCode;
            return false;
        }

        return true;
    }

    protected function resetDeadTasks() : void
    {
        $deadTasks = $this->taskRepository->getRunningTasks();
        foreach ($deadTasks as $task)
        {
            Log::error('Tasks', 'Execute', 'Task '.$task->name.' was no longer running and is resetted');
            $this->resetTask($task);
        }
    }

    protected function finishTask(Task $task, $output) : void
    {
        if ($task->keep) {
            usleep($task->interval * 1000);
            $this->taskRepository->setRunning($task, false);
            return;
        }

        Log::info('Tasks', 'Finished', $task->name . ' has successfully executed');
        $this->cleanupTask($task);
    }

    protected function errorTask(Task $task, Throwable $exception) : void
    {
        $errorLogId = Log::error('Tasks', 'Execute', $exception->getMessage(), $exception);

        $this->taskRepository->setError($task, true, $errorLogId);
        $this->resetTask($task);
    }

    protected function cleanupTask(Task $task) : void
    {
        $this->taskRepository->cleanUp($task);
    }

    protected function resetTask(Task $task) : void
    {
        $this->taskRepository->reset($task);
    }
}
