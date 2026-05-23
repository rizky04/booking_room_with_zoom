<?php

return [
    'accounts' => [
        1 => [
            'client_id'     => env('ZOOM_CLIENT_ID'),
            'client_secret' => env('ZOOM_CLIENT_SECRET'),
            'account_id'    => env('ZOOM_ACCOUNT_ID'),
        ],
        2 => [
            'client_id'     => env('ZOOM_2_CLIENT_ID'),
            'client_secret' => env('ZOOM_2_CLIENT_SECRET'),
            'account_id'    => env('ZOOM_2_ACCOUNT_ID'),
        ],
    ],

    // kept for backwards compat with isConfigured()
    'client_id'     => env('ZOOM_CLIENT_ID'),
    'client_secret' => env('ZOOM_CLIENT_SECRET'),
    'account_id'    => env('ZOOM_ACCOUNT_ID'),

    'base_url'  => env('ZOOM_API_BASE_URL', 'https://api.zoom.us/v2'),
    'token_url' => 'https://zoom.us/oauth/token',
    'cache_ttl' => 3500,
];
