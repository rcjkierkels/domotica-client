<?php

namespace App\Services;

use App\Models\Log;
use Cvuorinen\Raspicam\Raspistill;

class CameraService
{

    public function __construct() {}

    public function takePhoto() : string
    {
        $tmpfile = tempnam(storage_path(), 'raspistill-');

        $camera = new Raspistill();
        $camera->timeout(1)
            ->rotate(90)
            ->quality(100)
            ->takePicture($tmpfile);

        $photo = file_get_contents($tmpfile);

        unlink($tmpfile);

        if (empty($photo)) {
            Log::error('CameraService', 'takePhoto', "Cannot take photo");
            return null;
        }

        return $photo;
    }


}
