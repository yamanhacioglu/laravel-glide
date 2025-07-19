<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Image Source
    |--------------------------------------------------------------------------
    |
    | The source directory where images are stored. This should be an absolute
    | path to the directory where your images are located.
    |
    */
    'source' => storage_path('app/public'),

    /*
    |--------------------------------------------------------------------------
    | Cache Path
    |--------------------------------------------------------------------------
    |
    | The cache directory where processed images will be stored. This should
    | be an absolute path to a writable directory.
    |
    */
    'cache' => storage_path('app/glide'),

    /*
    |--------------------------------------------------------------------------
    | Cache Path Prefix
    |--------------------------------------------------------------------------
    |
    | Optional path prefix for cache directory. This is useful if you want
    | to organize cache files in subdirectories.
    |
    */
    'cache_path_prefix' => null,

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL prefix for image URLs. This will be prepended to all
    | generated image URLs.
    |
    */
    'prefix' => '/glide',

    /*
    |--------------------------------------------------------------------------
    | Maximum Image Size
    |--------------------------------------------------------------------------
    |
    | The maximum size for image width and height in pixels. Set to null
    | to allow unlimited size.
    |
    */
    'max_image_size' => 2000*2000,

    /*
    |--------------------------------------------------------------------------
    | Default Quality
    |--------------------------------------------------------------------------
    |
    | The default quality for image compression (1-100).
    |
    */
    'quality' => 90,

    /*
    |--------------------------------------------------------------------------
    | Response Factory
    |--------------------------------------------------------------------------
    |
    | The response factory class to use for generating HTTP responses.
    |
    */
    'response' => League\Glide\Responses\LaravelResponseFactory::class,

    /*
    |--------------------------------------------------------------------------
    | Watermarks
    |--------------------------------------------------------------------------
    |
    | Path to watermark files.
    |
    */
    'watermarks' => storage_path('app/watermarks'),

    /*
    |--------------------------------------------------------------------------
    | Presets
    |--------------------------------------------------------------------------
    |
    | Preset configurations for common image manipulations.
    |
    */
    'presets' => [
        'small' => [
            'w' => 200,
            'h' => 200,
            'fit' => 'crop',
        ],
        'medium' => [
            'w' => 600,
            'h' => 400,
            'fit' => 'crop',
        ],
        'large' => [
            'w' => 1200,
            'h' => 800,
            'fit' => 'crop',
        ],
    ],
];
