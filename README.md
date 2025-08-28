# apks_mvcoop PHP MVC Micro Framework

This repository is a minimal PHP MVC starting point to bootstrap future PHP web applications.

Features:
- Public entry point (public/index.php)
- Front Controller with simple routing
- Basic MVC structure: app/Controllers, app/Models, app/Views
- Simple Response/Request helpers
- PSR-4 autoloading via Composer
- MySQL database bootstrap via PDO (config/database.php)

## Requirements
- PHP 7.4+
- PHP extensions: PDO, pdo_mysql
- MySQL 5.7+ or MariaDB 10+
- Composer

## Getting Started
1. Install dependencies and generate autoloader:
   composer install

2. Configure environment variables:
   - Create a .env file (this project expects .env to exist). You can copy from .env.example and adjust values (APP_URL, DB_*).
   - For safety, database credentials are only read from .env; config/database.php is not used for secrets.

3. Create a database (default "db_mvcoop" from .env) and grant access.

4. Start a local PHP server from the project root (ensure your PHP is 7.4+):
   php -S localhost:8000 -t public

5. Open your browser at:
   http://localhost:8000

## Project Structure
- app/
  - Core/ (Router, Controller, Request, Response, Database, Container, EventDispatcher)
  - Controllers/
  - Models/
  - Views/
  - Modules/
- config/
  - modules.php
  - database.php (no secrets; only optional non-secret PDO options)
- public/
  - index.php (front controller)

## Create a Controller
Create file app/Controllers/HomeController.php with the following content:

```
<?php
namespace App\\Controllers;

use App\\Core\\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return $this->view('home', ['title' => 'Welcome']);
    }
}
```

## Views and Layout
This starter uses a main layout at app/Views/layout.phtml. Your view files should output content only; the layout will wrap them.

- Layout file: app/Views/layout.phtml (controls <head>, styles, header)
- Example view: app/Views/home.php (content-only)

Create file app/Views/home.php with the following content:

```
<p>You are running the <strong>apks_mvcoop</strong> PHP MVC starter.</p>
<p class="muted">Edit <code>app/Views/home.php</code> to change this page.</p>
```

## Routing
Routes are defined by URL path to Controller@method mapping in public/index.php. Update the routes array to fit your app.

### 404 Page
A friendly 404 page is provided at app/Views/404.phtml and is rendered via the router fallback with HTTP 404 status.

## Modules
This framework supports simple plug-in style modules.

- Define a module class that implements App\\Core\\ModuleInterface and place it under app/Modules/YourModule.
- Register the module class in config/modules.php.
- In your module's register method, you can add routes to the Router.

Example:

1) Create app/Modules/Hello/HelloModule.php

```
<?php
namespace App\\Modules\\Hello;

use App\\Core\\ModuleInterface;
use App\\Core\\Router;

class HelloModule implements ModuleInterface
{
    public function register(Router $router)
    {
        $router->get('/hello', array('App\\Modules\\Hello\\HelloController', 'index'));
    }
}
```

2) Create a controller used by the route at app/Modules/Hello/HelloController.php

```
<?php
namespace App\\Modules\\Hello;

use App\\Core\\Controller;

class HelloController extends Controller
{
    public function index()
    {
        return $this->view('modules/hello/index', array('title' => 'Hello Module'));
    }
}
```

3) Create the view at app/Views/modules/hello/index.php and register the module in config/modules.php by adding:

```
return array(
    'App\\Modules\\Hello\\HelloModule',
);
```

After reloading, visit /hello to see the module route.

## Database usage
A PDO connection is initialized at boot using values from .env (DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS, DB_CHAR). The config/database.php file is not used for credentials. You can retrieve the PDO from the container or via the Database helper:

- From a controller or module using the container:
  $pdo = $container->get('db.pdo');

- Or directly:
  $pdo = \App\Core\Database::pdo();

Example query:

```
$pdo = \App\Core\Database::pdo();
$stmt = $pdo->prepare('SELECT NOW() as now');
$stmt->execute();
$row = $stmt->fetch();
```

## Module communication
Modules can communicate in two safe, decoupled ways:

- Event bus (publish/subscribe): Use App\\Core\\EventDispatcher to emit and listen to events without hard dependencies.
- Service container: Use App\\Core\\Container to share/retrieve services by id (e.g., a reporting service).

The framework wires a shared Container under id "container" and the Router under "router"; an EventDispatcher is also shared and passed to modules on register().

Example (inside a Module::register):

```
public function register(\App\Core\Router $router, \App\Core\Container $container = null, \App\Core\EventDispatcher $events = null)
{
    // Subscribe
    if ($events) {
        $events->on('report.generated', function ($data) {
            // react to report event
        });
    }

    // Publish later from anywhere that has $events
    // $events->emit('report.generated', array('id' => 123));

    // Register or retrieve services
    if ($container) {
        $container->singleton('report.service', function ($c) {
            return new \App\Modules\Report\ReportService();
        });
        $svc = $container->get('report.service');
    }
}
```

## .env and configuration
- Create your .env from .env.example. Do NOT commit your real .env.
- Precedence: .env values override config files at runtime.
- Common keys:
  - APP_NAME, APP_ENV, APP_DEBUG, APP_URL
  - DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS, DB_CHAR (these must be set in .env)

Security note: .env is ignored via .gitignore and should not be served by your web server. Keep it outside the public/ directory (it already is at project root).

## Maintainer
- Company: APKS
- Author: Pattanapong Suwan
- Email: psuwan@yahoo.com

## License
MIT