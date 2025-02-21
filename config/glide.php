<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Source
    |--------------------------------------------------------------------------
    |
    | Here you can configure the storage location of the source images.
    |
    */

    'source' => public_path(),

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | Here you can configure the storage location for the cached images.
    |
    */

    'cache' => storage_path('framework/cache/glide'),

    /*
    |--------------------------------------------------------------------------
    | Driver
    |--------------------------------------------------------------------------
    |
    | The driver that will be used to create images. Can be set to gd or imagick.
    |
    */
    'driver' => 'gd',

    /*
    |--------------------------------------------------------------------------
    | URL prefix
    |--------------------------------------------------------------------------
    |
    | The prefix that will be used to build the URLs which serve the images.
    |
    */
    'prefix' => 'glide',

];
