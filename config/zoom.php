<?php

return [
    'client_id'     => env('ZOOM_CLIENT_ID'),
    'client_secret' => env('ZOOM_CLIENT_SECRET'),
    'account_id'    => env('ZOOM_ACCOUNT_ID'),
    'base_url'      => env('ZOOM_API_BASE_URL', 'https://api.zoom.us/v2'),
    'token_url'     => 'https://zoom.us/oauth/token',
    'cache_ttl'     => 3500, // seconds (slightly less than 1 hour to be safe)
];
