<?php

namespace App\Console\Commands;

use App\Models\ClientData;
use App\Models\Log;
use App\Services\GPIOService;
use Illuminate\Console\Command;

class checkGarageDoor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'client:checkGarageDoor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and log the status of the garage door';

    protected $gpioService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(GPIOService $GPIOService)
    {
        $this->gpioService = $GPIOService;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        while (1) {

            $clientID = 'GarageDoorChecker';

            $clientData = ClientData::where('client_id', $clientID)->pluck('data')->first();

            if (!empty($clientData)) {
                $clientData = json_decode($clientData);
            }

            $value = $this->gpioService->read(3);

            if ($value !== true && $value !== false) {
                return;
            }

            if ((int) $clientData->last_status !== (int) $value) {

                // New status
                if ($value === false) {
                    Log::info($clientID, 'closed', 'Garage deur is gesloten');
                } else {
                    Log::info($clientID, 'open', 'Garage deur is open');
                }

                ClientData::where('client_id', $clientID)->update(['data' => json_encode(['last_status' => (int) $value])]);
            }

            sleep(1);
        }



    }
}
