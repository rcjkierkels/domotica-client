<?php

namespace App\Repositories;

use App\Models\Task;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class TaskRepository
{
    protected $clientRepository;

    /** @var \App\Models\Client client */
    protected $client;

    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->client = $this->clientRepository->getClient();
    }

    public function getQueuedTasks() : Collection
    {
        return Task::where('client_id', $this->client->id)
            ->where('running', 0)
            ->where('error', 0)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function getRunningTasks() : Collection
    {
        return Task::where('client_id', $this->client->id)
            ->where('running', 1)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function getTasks() : Collection
    {
        return Task::where('client_id', $this->client->id)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function cleanUp(Task $task)
    {
        $task->delete();
    }

    public function reset(Task $task)
    {
        $this->setRunning($task, false);
    }

    public function setRunning(Task $task, bool $isRunning = true)
    {
        $task->running = $isRunning;
        $task->save();
    }

    public function setError(Task $task, bool $error, ?int $errorLogId = null)
    {
        $task->error = $error;
        $task->error_log_id = $errorLogId;
        $task->save();
    }

    public function getTaskById(int $id)
    {
        return Task::find($id);
    }



}