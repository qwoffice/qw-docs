### Dependencies

In order to get it working with laravel you need a way to automatically include geacy files based on file based routing 

- https://github.com/headerx/laravel-legacy-loader 

```php
<?php

namespace HeaderX\LegacyLoader\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LegacyController
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, $file)
    {
        $query_string = '';

        if (count(explode('?', $request->fullUrl())) > 1) {
            $query_string = explode('?', $request->fullUrl())[1];
        }
        parse_str($query_string, $_GET);

        $file = base_path(config('legacy-loader.file_path') . DIRECTORY_SEPARATOR . $this->getCleanPath($request->path()));

        if (! str_contains($request->path(), '.php')) {
            $file = str_replace(['.html', '.php', '.txt'], '', $file) . '.php';
        }
        if (file_exists($file)) {
            // ob_start();
            require_once($file);

        // return new Response(ob_get_clean());
        } else {

            /**
             * Todo: Exceptions
             */
            abort(404);
        }
    }

    protected function getCleanPath(string $path)
    {
        return str_replace(config('legacy-loader.route_prefix'), '', $path);
    }
}
```

And (requiring jetstream) you can load in iframe to simplify layout

- https://github.com/headerx/laravel-iframes


# symlinks and paths


the assets can be symlinked into the public directory by adding the following paths to `links` array in `config/filesystems.php`. You must first create
the directory `public/qwoffice`

```php
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

```

## Then set the links array to use the $links variable

```php
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

```

