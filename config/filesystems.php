<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],
        'export_uploads' => [
            'driver' => 'local',
            'root' => public_path('text_interface'),
        ],
        'export_dialogues_uploads' => [
            'driver' => 'local',
            'root' => public_path('dialogues'),
        ],
        'lang_uploads' => [
            'driver' => 'local',
            'root' => storage_path('/uploads/language/ico'),
        ],
        'episode_uploads' => [
            'driver' => 'local',
            'root' => storage_path('/uploads/episode'),
        ],
        'chapter_uploads_image' => [
            'driver' => 'local',
            'root' => storage_path('/uploads/chapter/image'),
        ],
        'chapter_uploads_background' => [
            'driver' => 'local',
            'root' => storage_path('/uploads/chapter/background'),
        ],
        'sub_chapter_uploads_background' => [
            'driver' => 'local',
            'root' => storage_path('/uploads/sub-chapter/background'),
        ],
        'video' => [
            'driver' => 'local',
            'root' => public_path('video'),
        ],
        'audio' => [
            'driver' => 'local',
            'root' => public_path('audio'),
        ],
        'music' => [
            'driver' => 'local',
            'root' => public_path('music'),
        ],
        'word/audio' => [
            'driver' => 'local',
            'root' => public_path('word/audio'),
        ],
        'word/img' => [
            'driver' => 'local',
            'root' => public_path('word'),
        ],
        'dialogue/audio' => [
            'driver' => 'local',
            'root' => public_path('dialogue/audio'),
        ],
        'game/audio' => [
            'driver' => 'local',
            'root' => public_path('game/audio'),
        ],
        'backgrounds' => [
            'driver' => 'local',
            'root' => public_path('backgrounds'),
        ],
        'animations' => [
            'driver' => 'local',
            'root' => public_path('animations'),
        ],
        'quiz' => [
            'driver' => 'local',
            'root' => public_path('quiz'),
        ],
        'emotions' => [
            'driver' => 'local',
            'root' => public_path('emotions'),
        ],
        'status' => [
            'driver' => 'local',
            'root' => public_path('status'),
        ],
        'season' => [
            'driver' => 'local',
            'root' => public_path('season'),
        ],
        'episode' => [
            'driver' => 'local',
            'root' => public_path('episode'),
        ],
        'episode/additional' => [
            'driver' => 'local',
            'root' => public_path('episode/additional'),
        ],
        'chapter' => [
            'driver' => 'local',
            'root' => public_path('chapter/image'),
        ],
        'sub-chapter' => [
            'driver' => 'local',
            'root' => public_path('sub-chapter'),
        ],
        'language' => [
            'driver' => 'local',
            'root' => public_path('language/icon'),
        ],

        'sftp' => [
            'driver' => 'sftp',
            'host' => env('SFTP_HOST'),
            'root'=>'/mnt/langlyphpapistorage',
            'username' => env('SFTP_USERNAME'),
            'password' => env('SFTP_PASSWORD'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
        ],
//        'gcs' => [
//            'driver' => 'gcs',
//            'project_id' => env('GCP_PROJECT_ID'), // Your Google Cloud Project ID
//            'key_file' => storage_path('engley-41e24-bdfedfd2885f.json'), // Path to your service account JSON
//            'bucket' => env('GCS_BUCKET'), // The name of your bucket
////            'path_prefix' => 'php_statics_public', // optional: directory prefix inside the bucket
////            'path_prefix' => null, // optional: directory prefix inside the bucket
//            'storage_api_uri' => env('GCS_API_URI', null), // optional: Google Storage API endpoint
//        ],
        'gcs' => [
            'driver' => 'gcs',
            'project_id' => env('GCP_PROJECT_ID'),
            'key_file' => storage_path('engley-41e24-bdfedfd2885f.json'),
            'bucket' => env('GCS_BUCKET'),
            'path_prefix' => null,
            'visibility' => null,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
