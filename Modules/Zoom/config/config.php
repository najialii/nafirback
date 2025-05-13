<?php

return [
    'name' => 'Zoom',
    'accountId' => env('ZOOM_ACCOUNT_ID'),
    'clientId' => env('ZOOM_CLIENT_ID'),
    'grantType' => env('ZOOM_GRANT_TYPE', 'accountCredentials'),
    'clientSecret' => env('ZOOM_CLIENT_SECRET'),
    'baseUrl' => env('ZOOM_API_BASE_URL', 'https://api.zoom.us/v2'),
    'tokenLife' => 60 * 60 * 24 * 7,
    'authenticationMethod' => 'jwt',
    'maxApiCallsPerRequest' => '5'
];
