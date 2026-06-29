<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Meta Pixel (browser) and Conversion API (server)
    |--------------------------------------------------------------------------
    |
    | Configure via .env. Never hardcode pixel IDs or access tokens in code.
    |
    */

    'pixel_id' => env('META_PIXEL_ID'),

    'access_token' => env('META_ACCESS_TOKEN'),

    'test_event_code' => env('META_TEST_EVENT_CODE'),

    'enable_pixel' => (bool) env('META_ENABLE_PIXEL', false),

    'enable_capi' => (bool) env('META_ENABLE_CAPI', false),

    'api_version' => env('META_API_VERSION', 'v21.0'),

];
