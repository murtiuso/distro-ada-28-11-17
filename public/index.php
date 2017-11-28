<?php
require_once '../vendor/autoload.php';
// require .env; // no hace falta importar esto

use Phroute\Phroute\RouteCollector;
use Illuminate\Database\Capsule\Manager as Capsule;

// Punto de entrada a la aplicación
require_once '../arrays.php';
require_once '../helpers.php';

$baseDir = str_replace(
    basename($_SERVER['SCRIPT_NAME']),
    '',
    $_SERVER['SCRIPT_NAME']);

$baseUrl = "http://" . $_SERVER['HTTP_HOST'] . $baseDir;
define('BASE_URL', $baseUrl);

$dotenv = new Dotenv\Dotenv(__DIR__. '/..'); // /.. para que vaya al directorio previo. Si se pone .. a la hora de mostrar la ruta, acaba ponendo .. en la misma, no mostrando la carpeta madre.
// en punto después del __DIR__ es obligatorio aunque no se vea en la documentación
$dotenv->load();


// Instancia de Eloquent
$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => getenv('DB_HOST'),
    'database'  => getenv('DB_NAME'),
    'username'  => getenv('DB_USER'),
    'password'  => getenv('DB_PASS'),
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$route = $_GET['route'] ?? "/";

$router = new RouteCollector();

$router->controller('/', App\Controllers\HomeController::class);
$router->controller('/distros', App\Controllers\DistrosController::class);

$dispatcher = new Phroute\Phroute\Dispatcher($router->getData());

$method = $_REQUEST['_method'] ?? $_SERVER['REQUEST_METHOD'];

$response = $dispatcher->dispatch($method, $route);

// Print out the value returned from the dispatched function
echo $response;