<?php

namespace App\Repositories;

use App\Models\Action;
use App\Models\Event;
use App\Models\Task;
use App\Notifications\ActionExecution;

class EventRepository
{
    protected $clientRepository;
    protected $actionRepository;

    /** @var \App\Models\Client client */
    protected $client;

    public function __construct(ClientRepository $clientRepository, ActionRepository $actionRepository)
    {
        $this->actionRepository = $actionRepository;
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

        $evaluatedNotificationActions = $this->actionRepository->getSpecificActionsForEvent($event, Action::NotifiableTypes);

        /** @var Action $action */
        foreach($evaluatedNotificationActions as $action)
        {
            $action->notify(new ActionExecution($action));
        }
    }

}
