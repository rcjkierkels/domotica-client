<?php

namespace App\Console\Commands;

use App\Models\Log;
use App\Models\Task;
use App\Repositories\TaskRepository;
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

    protected $taskRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $pool = Pool::create();

        while(self::FOREVER)
        {

            $tasks = $this->taskRepository->getQueuedTasks();

            /** @var Task $task */
            foreach($tasks as $task)
            {
                $taskClass = 'App\Tasks\\'.$task->name;

                if (!class_exists($taskClass)) {
                    Log::error('Tasks', 'Run', 'Cannot run unknown task: '.$task->name);
                    $this->cleanupTask($task);
                    continue;
                }

                $this->taskRepository->setRunning($task);

                $pool->add(new $taskClass($task))
                     ->then(function($output) use ($task) { $this->finishTask($task, $output); })
                     ->catch(function(Throwable $exception) use ($task) { $this->errorTask($task, $exception); });


            }

            sleep(1);
        }
    }

    protected function finishTask(Task $task, $output)
    {
        if ($task->keep) {
            usleep($task->interval * 1000);
            $this->taskRepository->setRunning($task, false);
            return;
        }

        Log::info('Tasks', 'Finished', $task->name . ' has successfully executed');
        $this->cleanupTask($task);
    }

    protected function errorTask(Task $task, Throwable $exception)
    {
        Log::error('Tasks', 'Execute', $exception->getMessage());
        $this->cleanupTask($task);
    }

    protected function cleanupTask(Task $task)
    {
        $this->taskRepository->cleanUp($task);
    }
}
