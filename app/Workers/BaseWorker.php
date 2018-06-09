<?php

namespace App\Workers;

abstract class BaseWorker
{
    protected $data;
    protected $task;

    public function __construct(\App\Models\Task $task)
    {
        $this->data = new \stdClass();

        if (!empty($task->data)) {
            $data = json_decode($task->data);
            $this->data = isset($data->client) ? $data->client : null;
        }

        $this->task = $task;

        $this->configure();
    }

    public abstract function run();

    protected abstract function configure();

    protected function persistData()
    {
        if (empty($this->task->data) && empty($this->data)) {
            return;
        } else if (empty($this->task->data)) {
            $this->task->data = json_encode([
                'client' => $this->data
            ]);
        } else {
            $data = json_decode($this->task->data);
            $data->client = $this->data;
            $this->task->data = json_encode($data);
        }

        $this->task->save();
    }

}