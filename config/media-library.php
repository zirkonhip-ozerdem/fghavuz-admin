<?php

return [
    'disk_name' => env('MEDIA_DISK', 'public'),

    'max_file_size' => 1024 * 1024 * (int) env('MEDIA_MAX_DOCUMENT_SIZE', 20480) / 1024,

    'queue_conversions_by_default' => env('QUEUE_CONVERSIONS_BY_DEFAULT', false),

    'queue_name' => '',

    'queue_connection_name' => env('QUEUE_CONNECTION', 'sync'),

    'media_model' => Spatie\MediaLibrary\MediaCollections\Models\Media::class,

    'temporary_url_default_lifetime' => 5,

    'image_generators' => [
        Spatie\MediaLibrary\Conversions\ImageGenerators\Image::class,
        Spatie\MediaLibrary\Conversions\ImageGenerators\Webp::class,
        Spatie\MediaLibrary\Conversions\ImageGenerators\Pdf::class,
        Spatie\MediaLibrary\Conversions\ImageGenerators\Svg::class,
    ],

    'image_optimizers' => [],

    'image_driver' => env('IMAGE_DRIVER', 'gd'),

    'ffmpeg_path' => env('FFMPEG_PATH', '/usr/bin/ffmpeg'),
    'ffprobe_path' => env('FFPROBE_PATH', '/usr/bin/ffprobe'),

    'path_generator' => Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator::class,
    'custom_path_generators' => [],
    'url_generator' => Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator::class,

    'moves_media_on_update' => false,
    'version_urls' => false,

    'responsive_images' => [
        'width_calculator' => Spatie\MediaLibrary\ResponsiveImages\WidthCalculator\FileSizeOptimizedWidthCalculator::class,
        'use_tiny_placeholders' => true,
        'tiny_placeholder_generator' => Spatie\MediaLibrary\ResponsiveImages\TinyPlaceholderGenerator\Blurred::class,
    ],

    'enable_vapor_uploads' => env('ENABLE_VAPOR_UPLOADS', false),

    'default_loading_attribute_value' => null,
    'prefix' => '',
    'generate_thumbnails_for_temporary_uploads' => env('MEDIA_GENERATE_TEMPORARY_THUMBNAILS', false),

    'remote' => [
        'extra_headers' => [
            'CacheControl' => 'max-age=604800',
        ],
    ],

    'jobs' => [
        'perform_conversions' => Spatie\MediaLibrary\Conversions\Jobs\PerformConversionsJob::class,
        'generate_responsive_images' => Spatie\MediaLibrary\ResponsiveImages\Jobs\GenerateResponsiveImagesJob::class,
    ],

    'media_downloader' => Spatie\MediaLibrary\Downloaders\DefaultDownloader::class,
];
