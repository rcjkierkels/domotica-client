<?php

namespace App\Async;

use App\Models\Log;
use Spatie\Async\Task;
use STS\Backoff\Backoff;
use STS\Backoff\Strategies\ExponentialStrategy;

class TaskManager extends Task
{
    protected $task;
    protected $backoff;

    protected const BACKOFF_MAX_RETRY_ATTEMPTS = 5;
    protected const BACKOFF_BASETIME = 1500; // msec

    public function __construct(\App\Models\Task $task, Backoff $backoff)
    {
        $this->task = $task;
        $this->backoff = $backoff;

        $this->backoff->setMaxAttempts(self::BACKOFF_MAX_RETRY_ATTEMPTS);
    }

    public function configure()
    {
        $workerClassName = 'App\Workers\\'.$this->task->name.'Worker';

        if (!class_exists($workerClassName)) {
            throw new \Exception('Cannot run unknown task: '.$this->task->name.' because no worker exists');
        }
    }

    public function run()
    {
        $output = [];
        $returnVar = -1;

        $this->backoff
            ->setStrategy(new ExponentialStrategy(self::BACKOFF_BASETIME))
            ->enableJitter()
            ->setErrorHandler($this->errorHandler)
            ->run(function() use ($output, $returnVar) {

                exec('php '.__DIR__.'/../../artisan client:execute '.$this->task->id, $output, $returnVar);

                if ($returnVar !== 0) {
                    throw new \Exception(implode("\n", $output));
                }

            });

    }

    protected function errorHandler(\Exception $exception, $attempt, $maxAttempts) : void
    {
        Log::error('TaskManager', 'Execute', "Failed restarting task '{$this->task->name} [{$this->task->id}] after {$maxAttempts} attempts");

        throw $exception;
    }

}