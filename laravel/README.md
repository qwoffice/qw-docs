### Dependencies

In order to get it working with laravel you need a way to automatically include legacy files based on file based routing 

- https://github.com/headerx/laravel-legacy-loader 

Here is the relevant code:

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
And route

```php

<?php

use HeaderX\LegacyLoader\Http\Controllers\LegacyController;
use Illuminate\Support\Facades\Route;

Route::any(config('legacy-loader.route_prefix') . '/{path}', LegacyController::class)
    ->where('path', '(.*)')
    ->middleware(config('legacy-loader.middleware'))
    ->name('legacy');
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
### Database and connections

You will need company and qwmenus database connections. You can updated the connections array in `config/database.php`

```php
<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'company'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        // 'sqlite' => [
        //     'driver' => 'sqlite',
        //     'url' => env('DATABASE_URL'),
        //     'database' => env('SQLITE_DATABASE', database_path('database.sqlite')),
        //     'prefix' => '',
        //     'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        // ],

        'qwmenus' => [
            'driver' => env('QW_DATABASE_DRIVER', 'mysql'),
            'url' => env('QW_DATABASE_URL'),
            'host' => env('QW_DB_HOST', '127.0.0.1'),
            'port' => env('QW_DB_PORT', '3306'),
            'database' => env('QW_DB_DATABASE', 'forge'),
            'username' => env('QW_DB_USERNAME', 'forge'),
            'password' => env('QW_DB_PASSWORD', ''),
            'unix_socket' => env('QW_DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
            'dump' => [
                'dump_binary_path' => '/usr/bin', // only the path, so without `mysqldump` or `pg_dump`
                'use_single_transaction',
                'timeout' => 60 * 5, // 5 minute timeout
                // 'add_extra_option' => '--column_statistics=0', // for example '--column_statistics=0'
            ],
        ],

        'company' => [
            'driver' => env('DB_COMPANY_DRIVER', 'mysql'),
            'url' => env('DATABASE_URL'),
            'host' => env('DB_COMPANY_HOST', 'localhost'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_COMPANY_DATABASE', 'digpacbak'),
            'username' => env('DB_COMPANY_USERNAME', ''),
            'password' => env('DB_COMPANY_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'modes' => [
                //'ONLY_FULL_GROUP_BY', // Disable this to allow grouping by one column
                'STRICT_TRANS_TABLES',
                'NO_ZERO_IN_DATE',
                'NO_ZERO_DATE',
                'ERROR_FOR_DIVISION_BY_ZERO',
                'NO_AUTO_CREATE_USER',
                'NO_ENGINE_SUBSTITUTION',
            ],
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
            'dump' => [
                'dump_binary_path' => '/usr/bin', // only the path, so without `mysqldump` or `pg_dump`
                'use_single_transaction',
                'timeout' => 60 * 5, // 5 minute timeout
                // 'add_extra_option' => '--column_statistics=0', // for example '--column_statistics=0'
            ],
        ],

        // 'pgsql' => [
        //     'driver' => 'pgsql',
        //     'url' => env('DATABASE_URL'),
        //     'host' => env('DB_HOST', '127.0.0.1'),
        //     'port' => env('DB_PORT', '5432'),
        //     'database' => env('DB_DATABASE', 'forge'),
        //     'username' => env('DB_USERNAME', 'forge'),
        //     'password' => env('DB_PASSWORD', ''),
        //     'charset' => 'utf8',
        //     'prefix' => '',
        //     'prefix_indexes' => true,
        //     'schema' => 'public',
        //     'sslmode' => 'prefer',
        // ],

        // 'sqlsrv' => [
        //     'driver' => 'sqlsrv',
        //     'url' => env('DATABASE_URL'),
        //     'host' => env('DB_HOST', 'localhost'),
        //     'port' => env('DB_PORT', '1433'),
        //     'database' => env('DB_DATABASE', 'forge'),
        //     'username' => env('DB_USERNAME', 'forge'),
        //     'password' => env('DB_PASSWORD', ''),
        //     'charset' => 'utf8',
        //     'prefix' => '',
        //     'prefix_indexes' => true,
        // ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],

    ],

];
```

env example

```ini

APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
DEBUGBAR_ENABLED=true
APP_URL=http://localhost

LOG_CHANNEL=stack

DB_CONNECTION=company
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dev_master_copy
DB_USERNAME=root
DB_PASSWORD=

DB_COMPANY_HOST=127.0.0.1
DB_COMPANY_USERNAME=root
DB_COMPANY_PASSWORD=
DB_COMPANY_DATABASE=dev_master_copy


BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=redis
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=""
REDIS_PORT=6379

MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=null
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

QW_DB_HOST=127.0.0.1
QW_DB_PORT=3306
QW_DB_USERNAME=root
QW_DB_PASSWORD=
QW_DB_DATABASE=dev_qwmenus

FORWARD_DB_PORT=3306
APP_LOGO='storage/stock-images/logo_qwo.png'
ACTIVITY_LOGGER_DB_CONNECTION=company
COMPANY_FILE_ROOT=/storage/.server-dev
PAYABLES_FILE_ROOT=Payables
COMPANY_FILE_DRIVER=local


IGNITION_EDITOR="vscode"

TESTS_DB_USERNAME=root
TESTS_DB_PASSWORD=''

COMPANY_LOGO_PATH='office/var/www/qwoffice/print/DigLogo.jpg'

LEGACY_ROUTE_PREFIX=qwoffice
LEGACY_FILE_PATH=resources/legacy/qwoffice
```

get required migrations

```bash
curl -o $(pwd)/database/schema/company-schema.dump https://raw.githubusercontent.com/qwoffice/qw-docs/main/laravel/database/schema/company-schema.dump
```

```bash
curl -o $(pwd)/database/schema/qwmenus-schema.dump https://raw.githubusercontent.com/qwoffice/qw-docs/main/laravel/database/schema/qwmenus-schema.dump
```
