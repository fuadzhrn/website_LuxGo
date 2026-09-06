<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Image Upload Rules
    |--------------------------------------------------------------------------
    |
    | One definition shared by every image field, so the editors added in later
    | stages cannot drift into slightly different limits. SVG is excluded on
    | purpose: it can carry script and is not needed for photography.
    |
    */

    'images' => [
        'accept' => 'image/jpeg,image/png,image/webp',
        'extensions' => ['jpg', 'jpeg', 'png', 'webp'],
        'max_kilobytes' => 4096,
        'rules' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Content Language Labels
    |--------------------------------------------------------------------------
    |
    | How each supported locale is named in the admin UI. The locales themselves
    | come from config/locales.php — this only labels them.
    |
    */

    'locale_labels' => [
        'id' => 'Bahasa Indonesia',
        'en' => 'English',
    ],

];
