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
    | Temporary: the site content is still English while the Indonesian copy is
    | being prepared. This becomes 'id' at the content localization stage.
    |
    */

    'default' => 'en',

];
