<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google OAuth Client ID
    |--------------------------------------------------------------------------
    |
    | The client ID for your Google Cloud project. You can obtain this from
    | the Google Cloud Console under APIs & Services > Credentials.
    |
    */
    'client_id' => env('GOOGLE_CLIENT_ID'),

    /*
    |--------------------------------------------------------------------------
    | Google OAuth Client Secret
    |--------------------------------------------------------------------------
    |
    | The client secret for your Google Cloud project. Keep this value secure
    | and never commit it to version control.
    |
    */
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Google OAuth Redirect URI
    |--------------------------------------------------------------------------
    |
    | The URI where Google will redirect after authentication. This must match
    | exactly what is configured in your Google Cloud Console.
    |
    */
    'redirect_uri' => env('GOOGLE_REDIRECT_URI', '/calendar/google/callback'),

    /*
    |--------------------------------------------------------------------------
    | Google Calendar Scopes
    |--------------------------------------------------------------------------
    |
    | The OAuth scopes required for Google Calendar integration.
    |
    */
    'scopes' => [
        \Google\Service\Calendar::CALENDAR_EVENTS,
        \Google\Service\Calendar::CALENDAR_READONLY,
    ],

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | The name of your application as it will appear in Google's consent screen.
    |
    */
    'application_name' => env('GOOGLE_APPLICATION_NAME', 'Habit Tracks'),

    /*
    |--------------------------------------------------------------------------
    | Access Type
    |--------------------------------------------------------------------------
    |
    | Set to 'offline' to receive a refresh token for long-term access.
    |
    */
    'access_type' => 'offline',

    /*
    |--------------------------------------------------------------------------
    | Prompt
    |--------------------------------------------------------------------------
    |
    | Set to 'consent' to always show the consent screen and get a refresh token.
    |
    */
    'prompt' => 'consent',
];
