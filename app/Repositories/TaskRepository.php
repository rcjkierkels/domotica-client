<?php

namespace App\Repositories;

use App\Models\Task;
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

    public function setRunning(Task $task, bool $isRunning = true)
    {
        $task->running = $isRunning;
        $task->save();
    }



}