<?php

namespace App\Workers;

use App\Collections\EventAttachmentCollection;
use App\Objects\EventAttachment;
use App\Repositories\EventRepository;
use App\Services\CameraService;

class CameraWorker extends BaseWorker
{
    /** @var EventRepository $eventRepository */
    protected $eventRepository;

    /** @var CameraService */
    protected $cameraService;

    /** @var EventAttachmentCollection */
    protected $eventAttachmentCollection;

    public function configure()
    {
        $this->cameraService = app()->make(CameraService::class);
        $this->eventRepository = app()->make(EventRepository::class);
        $this->eventAttachmentCollection = app()->make(EventAttachmentCollection::class);
    }

    public function run()
    {
        $photo = $this->cameraService->takePhoto();
        $state = (int) !empty($photo);

        if (!empty($photo)) {
            $eventAttachment = new EventAttachment();
            $eventAttachment->data = $photo;
            $eventAttachment->mime_type = 'image/jpeg';

            $this->eventAttachmentCollection->push($eventAttachment);
        }

        $this->eventRepository->triggerEvent($this->task, ['state' => $state], $this->eventAttachmentCollection);
    }

}
