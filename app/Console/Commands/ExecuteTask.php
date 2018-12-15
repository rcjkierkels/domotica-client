<?php

namespace App\Console\Commands;

use App\Models\Log;
use App\Models\Task;
use App\Repositories\TaskRepository;
use App\Tasks\BaseTask;
use App\Workers\BaseWorker;
use Illuminate\Console\Command;
use Spatie\Async\Pool;
use Throwable;

class ExecuteTask extends Command
{
    const FOREVER = 1;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'client:execute {task_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute specific task by activating worker';

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
        try {

            $task = $this->taskRepository->getTaskById($this->argument('task_id'));
            $workerClassName = 'App\Workers\\'.$task->name.'Worker';

            /** @var BaseWorker $workerClass */
            $workerClass = new $workerClassName($task);
            $workerClass->run();

        } catch (\Exception $e) {

            // Write exception to stdErr output so it can be catched by TaskManager
            fwrite(STDERR, $e->getMessage());
        }

    }

}
