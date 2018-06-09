<?php

namespace App\Async;

use Spatie\Async\Task;

class TaskManager extends Task
{
    protected $task;

    public function __construct(\App\Models\Task $task)
    {
        $this->task = $task;
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

        exec('php '.__DIR__.'/../../artisan client:execute '.$this->task->id, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new \Exception(implode("\n", $output));
        }
    }

}