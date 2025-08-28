<?php
namespace App\Core;

interface ModuleInterface
{
    /**
     * Called during application boot. Use this to register routes and perform any setup.
     *
     * @param Router $router The router to register routes on.
     * @param Container $container A simple service container exposing shared services.
     * @param EventDispatcher $events An event dispatcher for inter-module communication.
     * @return void
     */
    public function register(Router $router, Container $container = null, EventDispatcher $events = null);
}
