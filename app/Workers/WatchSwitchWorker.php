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
        $inputPin = config('gpio.pins.input')[0];
        $value = $this->gpioService->read($inputPin);

        if ($value !== true && $value !== false) {
            return;
        }

        if (!isset($this->data->lastSwitchStatus) || $this->data->lastSwitchStatus !== $value) {
            if ($value === false) {
                Log::info('SWITCH', 'closed', 'Switch is closed');
            } else {
                Log::info('SWITCH', 'open', 'Switch is open');
            }

            $this->data->lastSwitchStatus = $value;

            $this->eventRepository->triggerEvent($this->task, ['state' => (int) $this->data->lastSwitchStatus]);

            $this->persistData();
        }
    }

}