<?php

namespace App\Workers;

use App\Repositories\EventRepository;
use App\Services\CameraService;

class CameraWorker extends BaseWorker
{
    /** @var EventRepository $eventRepository */
    protected $eventRepository;

    /** @var CameraService */
    protected $cameraService;

    public function configure()
    {
        $this->cameraService = app()->make(CameraService::class);
        $this->eventRepository = app()->make(EventRepository::class);
    }

    public function run()
    {
        $photo = $this->cameraService->takePhoto();
        $state = (int) !empty($photo);

        $this->eventRepository->triggerEvent($this->task, ['state' => $state, 'photo' => $photo]);
    }

}
