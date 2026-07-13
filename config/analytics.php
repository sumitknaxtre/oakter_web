<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Google Analytics 4 (gtag.js)
    |--------------------------------------------------------------------------
    |
    | Configure via .env. Never hardcode measurement IDs in layouts.
    |
    */

    'enabled' => (bool) env('GA_ENABLED', false),

    'measurement_id' => env('GA_MEASUREMENT_ID'),

];
