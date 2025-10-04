<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Image Optimization Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the image optimization
    | service used throughout the application.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Image Quality
    |--------------------------------------------------------------------------
    |
    | The quality setting for JPEG compression (1-100).
    | Higher values mean better quality but larger file sizes.
    |
    */
    'quality' => env('IMAGE_QUALITY', 85),

    /*
    |--------------------------------------------------------------------------
    | Default Image Format
    |--------------------------------------------------------------------------
    |
    | The default format to convert images to. Supported: jpg, png, webp
    |
    */
    'format' => env('IMAGE_FORMAT', 'jpg'),

    /*
    |--------------------------------------------------------------------------
    | Image Sizes
    |--------------------------------------------------------------------------
    |
    | Define the different sizes to generate for responsive images.
    | Width in pixels, or null for original size.
    |
    */
    'sizes' => [
        'thumbnail' => 400,
        'medium' => 800,
        'large' => 1200,
        'original' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Validation
    |--------------------------------------------------------------------------
    |
    | Settings for validating uploaded image files.
    |
    */
    'validation' => [
        'max_size' => env('IMAGE_MAX_SIZE', 10 * 1024 * 1024), // 10MB
        'allowed_mimes' => ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'],
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'webp'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | Cache settings for serving images.
    |
    */
    'cache' => [
        'max_age' => env('IMAGE_CACHE_MAX_AGE', 31536000), // 1 year
        'etag_enabled' => env('IMAGE_ETAG_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback Image URLs
    |--------------------------------------------------------------------------
    |
    | Default placeholder images for different content types.
    |
    */
    'fallbacks' => [
        'project' => [
            'Campestres' => 'https://images.unsplash.com/photo-1500382017468-9049fed747ef',
            'Urbanos' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab',
            'Turísticos' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d',
            'default' => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa',
        ],
        'property' => 'https://images.unsplash.com/photo-1570129477492-45c003edd2be',
        'agent' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d',
        'blog' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab',
        'default' => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa',
    ],

    /*
    |--------------------------------------------------------------------------
    | CDN Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for CDN integration (future use).
    |
    */
    'cdn' => [
        'enabled' => env('CDN_ENABLED', false),
        'url' => env('CDN_URL', null),
        'driver' => env('CDN_DRIVER', 's3'), // s3, cloudfront, etc.
    ],

];