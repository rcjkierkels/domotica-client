<?php

namespace App\Services;

use App\Models\Log;
use Cvuorinen\Raspicam\Raspistill;

class CameraService
{

    public function __construct() {

        $this->CAMERA_APP = "raspistill ";

    }

    public function takePhoto() : string
    {
        $tmpfile = tempnam(storage_path());

        $camera = new Raspistill();
        $camera->timeout(1)
            ->rotate(90)
            ->quality(90)
            ->takePicture($tmpfile);

        $photo = file_get_contents($tmpfile);

        if (empty($photo)) {
            Log::error('CameraService', 'takePhoto', "Cannot take photo");
            return null;
        }

        return $photo;
    }


}
