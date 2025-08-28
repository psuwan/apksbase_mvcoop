<?php
use App\Core\Router;
use App\Core\Request;
use App\Core\ModuleManager;
use App\Core\Env;
use App\Core\Container;
use App\Core\EventDispatcher;
use App\Core\Database;

require dirname(__DIR__) . '/vendor/autoload.php';

// Load environment variables
$root = dirname(__DIR__);
Env::load($root . DIRECTORY_SEPARATOR . '.env');

$router = new Router();

// Register core/base routes
$router->get('/', array('App\\Controllers\\HomeController', 'index'));

// Load and register modules
$modules = array();
$configFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'modules.php';
if (file_exists($configFile)) {
    $modules = require $configFile;
}

// Initialize shared services
$container = new Container();
$events = new EventDispatcher();

// Database bootstrap: prefer .env only (do not read config/database.php for credentials)
$dbConfig = array(
    'host' => Env::get('DB_HOST'),
    'port' => Env::get('DB_PORT'),
    'database' => Env::get('DB_NAME'),
    'username' => Env::get('DB_USER'),
    'password' => Env::get('DB_PASS'),
    'charset' => Env::get('DB_CHAR'),
);
// If required keys are present (host, database, username), initialize DB
$hasRequired = !empty($dbConfig['host']) && !empty($dbConfig['database']) && !empty($dbConfig['username']);
if ($hasRequired) {
    try {
        Database::init($dbConfig);
        $container->instance('db.pdo', Database::pdo());
        $container->instance('db.config', $dbConfig);
    } catch (\Throwable $e) {
        // If DB fails, continue application; you can log $e->getMessage()
    }
}

$moduleManager = new ModuleManager($modules, $container, $events);
$moduleManager->registerAll($router);

// Fallback 404
$router->fallback(function () {
    http_response_code(404);
    // Render 404 view through the layout
    $ctrl = new class extends \App\Core\Controller { public function render404() { return $this->view('404', array('title' => '404 Not Found')); } };
    echo $ctrl->render404();
});

$router->dispatch(Request::fromGlobals());
