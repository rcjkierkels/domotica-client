<?php

namespace App\Workers;

use App\Models\Log;
use App\Repositories\EventRepository;
use App\Services\GPIOService;

class WatchSwitchWorker extends BaseWorker
{
    /** @var GPIOService $gpioService */
    protected $gpioService;

    /** @var EventRepository $eventRepository */
    protected $eventRepository;

    public function configure()
    {
        $this->gpioService = app()->make(GPIOService::class);
        $this->eventRepository = app()->make(EventRepository::class);
    }

    public function run()
    {
        $value = $this->gpioService->read(3);

        if ($value !== true && $value !== false) {
            return;
        }

        if (empty($this->data->lastSwitchStatus) || $this->data->lastSwitchStatus !== $value) {
            if ($value === false) {
                Log::info('SWITCH', 'closed', 'Switch is closed');
            } else {
                Log::info('SWITCH', 'open', 'Switch is open');
            }

            $this->eventRepository->triggerEvent('SWITCH', ['status' => (int) !$value]);

            $this->data->lastSwitchStatus = $value;

            $this->persistData();
        }
    }

}