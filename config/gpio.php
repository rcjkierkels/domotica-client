<?php

return [

    /*
    |--------------------------------------------------------------------------
    | GPIO Pins
    |--------------------------------------------------------------------------
    |
    | All available pins on GPIO
    |
    */

    'pins' => [
        'input' => [
            (int) env('GPIO_PIN_INPUT_1', 3),
        ]
    ],



];
