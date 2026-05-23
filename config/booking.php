<?php

return [
    'email_domain' => env('BOOKING_EMAIL_DOMAIN', null),

    'admin_email' => env('BOOKING_ADMIN_EMAIL', 'admin@company.com'),

    'rate_limit' => [
        'per_ip_per_hour' => env('BOOKING_RATE_LIMIT', 10),
        'enabled'         => true,
    ],

    'verification_token_expiry' => 24, // hours

    'booking_rules' => [
        'min_advance_hours' => 0,
        'max_advance_days'  => 90,
        'min_duration_min'  => 30,
        'max_duration_hours' => 8,
        'operating_hours'   => [
            'start' => '07:00',
            'end'   => '21:00',
        ],
        'allowed_days' => [1, 2, 3, 4, 5, 6], // Mon-Sat (0=Sun)
    ],

    'reminder_hours' => [24, 1], // Send reminders 24h and 1h before
];
