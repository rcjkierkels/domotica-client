<?php

namespace App\Repositories;

use App\Collections\EventAttachmentCollection;
use App\Models\Action;
use App\Models\Attachment;
use App\Models\Event;
use App\Models\Task;
use App\Notifications\ActionExecution;
use App\Objects\EventAttachment;

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

    public function triggerEvent(Task $task, $data, EventAttachmentCollection $attachments = null)
    {
        $event = new Event();
        $event->client_id = $this->client->id;
        $event->task_id = $task->id;
        $event->data = json_encode($data);
        $event->save();

        if (!empty($attachments)) {
            $this->addAttachments($event, $attachments);
        }

        $evaluatedNotificationActions = $this->actionRepository->getSpecificActionsForEvent($event, Action::NotifiableTypes);

        /** @var Action $action */
        foreach($evaluatedNotificationActions as $action)
        {
            $action->notify(new ActionExecution($action));
        }
    }

    public function addAttachments(Event $event, EventAttachmentCollection $eventAttachments)
    {
        foreach($eventAttachments as $attachment)
        {
            $this->addAttachment($event, $attachment);
        }
    }

    public function addAttachment(Event $event, EventAttachment $eventAttachment)
    {
        $attachment = new Attachment();
        $attachment->mime_type = $eventAttachment->mime_type;
        $attachment->data = $eventAttachment->data;
        $attachment->event_id = $event->id;
        $attachment->save();
    }

}
