<?php

return [

    'enabled' => env('UNICOMMERCE_ENABLED', false),

    'tenant' => env('UNICOMMERCE_TENANT'),

    'facility_code' => env('UNICOMMERCE_FACILITY_CODE'),

    'channel' => env('UNICOMMERCE_CHANNEL', 'Oakter Website'),

    'username' => env('UNICOMMERCE_USERNAME'),

    'password' => env('UNICOMMERCE_PASSWORD'),

    'client_id' => env('UNICOMMERCE_CLIENT_ID', 'my-trusted-client'),

    'shipping_method' => env('UNICOMMERCE_SHIPPING_METHOD', 'STD'),

    'order_code_prefix' => env('UNICOMMERCE_ORDER_CODE_PREFIX', 'OAKTER'),

];
