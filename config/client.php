<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Client Name
    |--------------------------------------------------------------------------
    |
    | The name of the domotica client. Can be any name and does not have to be
    | unique.
    |
    */

    'name' => env('CLIENT_NAME', 'NoName'),

    /*
    |--------------------------------------------------------------------------
    | Client Location
    |--------------------------------------------------------------------------
    |
    | Name of the location where this client is located. For example livingroom
    |
    */

    'location' => env('CLIENT_LOCATION', 'Unknown'),


];
