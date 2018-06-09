<?php

namespace App\Models;

use App\Repositories\ClientRepository;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    const TYPE_ERROR = 'error';
    const TYPE_WARNING = 'warning';
    const TYPE_INFO = 'info';
    const TYPE_DEBUG = 'debug';

    protected $table = 'log';

    public static function info($sSystem, $sEvent, $sMessage) {

        self::add(self::TYPE_INFO, $sSystem, $sEvent, $sMessage);

    }

    public static function debug($sSystem, $sEvent, $sMessage) {

        self::add(self::TYPE_DEBUG, $sSystem, $sEvent, $sMessage);

    }

    public static function alert($sSystem, $sEvent, $sMessage) {

        self::add(self::TYPE_WARNING, $sSystem, $sEvent, $sMessage);

    }

    public static function error($sSystem, $sEvent, $sMessage, \Exception $oException = null) {

        if (!empty($oException)) {
            //$sMessage += '\n\nFile:'.$oException->getFile().'\nLine:'.$oException->getLine();
        }

        self::add(self::TYPE_ERROR, $sSystem, $sEvent, $sMessage);
    }

    private static function add($sType, $sSystem, $sEvent, $sMessage)
    {
        /** @var ClientRepository $clientRepository */
        $clientRepository = app()->make(ClientRepository::class);
        $client = $clientRepository->getClient();

        $oLog = new Log();

        $oLog->source = 'client:'.$client->id;
        $oLog->system = $sSystem;
        $oLog->event = $sEvent;
        $oLog->message = $sMessage;
        $oLog->type = $sType;

        $oLog->debug_info = json_encode(debug_backtrace());
        $oLog->user_info = json_encode($_SERVER);

        $oLog->save();

    }
}
