<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Point\App;
use Point\View\Factory;
use Illuminate\Pagination\Paginator;
use Respect\Validation\Validator as v;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Capsule\Manager as Capsule;

session_start();

date_default_timezone_set('Asia/Jakarta');

$dotenv = new Dotenv\Dotenv(__DIR__ . '/..');
$dotenv->load();

LengthAwarePaginator::viewFactoryResolver(function () {
    return new Factory;
});

LengthAwarePaginator::defaultView('pagination/pagination.twig');

Paginator::currentPathResolver(function () {
    return isset($_SERVER['REQUEST_URI']) ? strtok($_SERVER['REQUEST_URI'], '?') : '/';
});

Paginator::currentPageResolver(function () {
    return $_GET['page'] ?? 1;
});

$capsule = new Capsule;
$capsule->addConnection([
    'driver'        => getenv('DB_DRIVER'),
    'host'          => getenv('DB_HOST'),
    'database'      => getenv('DB_DATABASE'),
    'username'      => getenv('DB_USERNAME'),
    'password'      => getenv('DB_PASSWORD'),
    'charset'       => getenv('DB_CHARSET'),
    'collation'     => getenv('DB_COLLATION'),
    'prefix'        => getenv('DB_PREFIX'),
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();


$app = new App;
$container = $app->getContainer();

v::with('Point\\Validation\\Rules\\');

require_once __DIR__ . '/middleware.php';

require_once __DIR__ . '/../routes/web.php';
