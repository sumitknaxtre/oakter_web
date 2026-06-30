<?php

return [

    'key_id' => env('RAZORPAY_KEY_ID'),
    'key_secret' => env('RAZORPAY_KEY_SECRET'),
    'currency' => 'INR',
    'company_name' => env('RAZORPAY_COMPANY_NAME', 'Oakter'),

    // Webhook signing secret from Razorpay Dashboard → Webhooks.
    'webhook_secret' => env('RAZORPAY_WEBHOOK_SECRET'),

];
