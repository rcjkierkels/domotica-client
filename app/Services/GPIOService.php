<?php

namespace App\Services;

use App\Models\Log;

class GPIOService
{

    protected $GPIO_WRITE;
    protected $GPIO_READ;

    const HIGH_VALUE = 1;
    const LOW_VALUE = 0;


    public function __construct() {
        $this->GPIO_WRITE = "python '".base_path('bin/gpio/write')."' ";
        $this->GPIO_READ = "python '".base_path('bin/gpio/read')."' ";
    }

    /**
     * @param $iPin
     * @return bool|null
     */
    public function read($iPin) {

        $aResponse = [];
        $iErrorCode = null;

        $mOutput = exec($this->GPIO_READ . $iPin, $aResponse, $iErrorCode);

        if ($iErrorCode) {
            Log::error('GPIOService', 'read', "Cannot read from pin {$iPin}");
            return null;
        }

        return boolval($mOutput);
    }

    /**
     * @param $iPin
     * @param $bValue
     */
    public function write($iPin, $bValue) {

        $bValue = (int) $bValue;

        exec($this->GPIO_WRITE . "{$iPin} {$bValue}", $aResponse, $iErrorCode);

    }


}
