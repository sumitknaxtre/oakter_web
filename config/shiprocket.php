<?php

return [

    'enabled' => env('SHIPROCKET_ENABLED', false),

    'base_url' => env('SHIPROCKET_BASE_URL', 'https://apiv2.shiprocket.in'),

    // API user credentials from Shiprocket → Settings → API.
    'email' => env('SHIPROCKET_API_EMAIL'),

    'password' => env('SHIPROCKET_API_PASSWORD'),

    // Pickup location nickname exactly as configured in Shiprocket.
    'pickup_location' => env('SHIPROCKET_PICKUP_LOCATION'),

    // Optional custom channel ID from Shiprocket channels API.
    'channel_id' => env('SHIPROCKET_CHANNEL_ID'),

    'order_id_prefix' => env('SHIPROCKET_ORDER_ID_PREFIX', 'OAKTER'),

    // Fallback only when a product has no package details saved yet.
    'package_length' => (float) env('SHIPROCKET_PACKAGE_LENGTH', 20),
    'package_breadth' => (float) env('SHIPROCKET_PACKAGE_BREADTH', 15),
    'package_height' => (float) env('SHIPROCKET_PACKAGE_HEIGHT', 10),
    'package_weight' => (float) env('SHIPROCKET_PACKAGE_WEIGHT', 1),

];
