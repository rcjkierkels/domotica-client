<?php

namespace App\Repositories;

use App\Models\Event;
use App\Models\Task;
use App\Notifications\EventCreated;

class EventRepository
{
    protected $clientRepository;

    /** @var \App\Models\Client client */
    protected $client;

    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->client = $this->clientRepository->getClient();
    }

    public function triggerEvent(Task $task, $data)
    {
        $event = new Event();
        $event->client_id = $this->client->id;
        $event->task_id = $task->id;
        $event->data = json_encode($data);
        $event->save();
        $event->notify(new EventCreated($event));
    }

}