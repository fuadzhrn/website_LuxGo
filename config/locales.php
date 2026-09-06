<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Supported Locales
    |--------------------------------------------------------------------------
    |
    | Every public URL is prefixed with one of these. Anything else is rejected
    | by the route constraint and again by the SetLocale middleware.
    |
    */

    'supported' => ['id', 'en'],

    /*
    |--------------------------------------------------------------------------
    | Default Locale
    |--------------------------------------------------------------------------
    |
    | Indonesian is the default for new visitors. English stays available at
    | /en and remains the fallback for any key that is missing a translation.
    |
    */

    'default' => 'id',

];
