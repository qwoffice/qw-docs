<?php

/** Default Links **/
$links = [
    public_path('storage') => storage_path('app/public'),
];

if (file_exists(public_path('qwoffice'))) {
    
    /** qwoffice root**/
    $qwoffice_dir_root = resource_path('legacy'); 
    
    /** Additional required symlinks **/
    $links = $links + [
        public_path('assets') => $qwoffice_dir_root . '/assets',
        public_path('glyphicons') => $qwoffice_dir_root . '/glyphicons',
        public_path('qwoffice/bootstrap') => $qwoffice_dir_root . '/qwoffice/bootstrap',
        public_path('qwoffice/css') => $qwoffice_dir_root . '/qwoffice/css',
        public_path('qwoffice/fonts') => $qwoffice_dir_root . '/qwoffice/fonts',
        public_path('qwoffice/js') => $qwoffice_dir_root . '/qwoffice/js',
        public_path('qwoffice/estimates') => $qwoffice_dir_root . '/qwoffice/estimates',
        public_path('qwoffice/static') => $qwoffice_dir_root . '/qwoffice/static',
        public_path('qwoffice/themes') => $qwoffice_dir_root . '/qwoffice/themes',
        public_path('qwoffice/chromeless_35.js') => $qwoffice_dir_root . '/qwoffice/chromeless_35.js',
    ];
}

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

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been set up for each driver as an example of the required values.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'bootstrap-cache' => [
            'driver' => 'local',
            'root' => base_path('bootstrap/cache'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
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
            'throw' => false,
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

    'links' => $links,

];
