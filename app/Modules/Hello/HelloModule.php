<?php
namespace App\Modules\Hello;

use App\Core\ModuleInterface;
use App\Core\Router;
use App\Core\Container;
use App\Core\EventDispatcher;

class HelloModule implements ModuleInterface
{
    public function register(Router $router, Container $container = null, EventDispatcher $events = null)
    {
        // Simple example route
        $router->get('/hello', array('App\\Modules\\Hello\\HelloController', 'index'));

        // Example: listen to a demo event (no-op by default)
        if ($events) {
            $events->on('hello.ping', function ($payload) {
                // For demo purposes: do nothing
            });
        }
    }
}
