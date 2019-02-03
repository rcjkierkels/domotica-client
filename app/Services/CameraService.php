<?php

namespace App\Services;

use App\Models\Log;

class CameraService
{

    public function __construct() {

        $this->CAMERA_APP = "raspistill ";

    }

    public function takePhoto() : string
    {

        $output = shell_exec($this->CAMERA_APP . "-q 100 -o -");

        if (empty($output)) {
            Log::error('CameraService', 'takePhoto', "Cannot take photo");
            return null;
        }

        return $output;
    }


}
