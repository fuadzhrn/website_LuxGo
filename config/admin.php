<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Image Upload Rules
    |--------------------------------------------------------------------------
    |
    | One definition shared by the Media Library and by every image field, so
    | the limits cannot drift apart between them. SVG is excluded on purpose:
    | it can carry script and is not needed for photography.
    |
    */

    'images' => [
        'accept' => 'image/jpeg,image/png,image/webp',
        'extensions' => ['jpg', 'jpeg', 'png', 'webp'],
        'max_kilobytes' => 10240,
        'rules' => ['image', 'mimetypes:image/jpeg,image/png,image/webp', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Media Storage
    |--------------------------------------------------------------------------
    |
    | CMS uploads live on the public disk, under one directory. Design assets in
    | public/assets are developer files and are never touched by the library.
    |
    */

    'media' => [
        'disk' => 'public',
        'directory' => 'luxgo/media',
        'per_page' => 24,
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
