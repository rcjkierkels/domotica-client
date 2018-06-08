<?php

namespace App\Tasks;

use App\Models\Log;
use App\Repositories\EventRepository;
use App\Services\GPIOService;
use Spatie\Async\Task;

class WatchSwitchTask extends Task
{
    /** @var GPIOService $gpioService */
    protected $gpioService;

    /** @var EventRepository $eventRepository */
    protected $eventRepository;

    protected $task;

    protected $data;

    public function __construct(\App\Models\Task $task)
    {
        if (!empty($task->data)) {
            $data = json_decode($task->data);
            $this->data = isset($data->client) ? $data->client : null;
        }

        $this->task = $task;
    }

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