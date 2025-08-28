<?php
namespace App\Core;

class ModuleManager
{
    /** @var ModuleInterface[] */
    private array $modules = array();

    /** @var Container */
    private Container $container;

    /** @var EventDispatcher */
    private EventDispatcher $events;

    /**
     * @param array $moduleClasses Array of fully-qualified class names implementing ModuleInterface
     * @param Container|null $container Shared container
     * @param EventDispatcher $events Event bus
     */
    public function __construct(array $moduleClasses = array(), Container $container = null, $events = null)
    {
        $this->container = $container ?: new Container();
        $this->events = $events ?: new EventDispatcher();

        foreach ($moduleClasses as $class) {
            if (class_exists($class)) {
                $instance = new $class();
                // Ensure it implements ModuleInterface (runtime check for 7.4)
                if ($instance instanceof ModuleInterface) {
                    $this->modules[] = $instance;
                }
            }
        }
    }

    /**
     * Access shared container.
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * Access event dispatcher.
     * @return EventDispatcher
     */
    public function getEvents(): EventDispatcher
    {
        return $this->events;
    }

    /**
     * Register all modules with the router.
     * @param Router $router
     * @return void
     */
    public function registerAll(Router $router)
    {
        // Register some core services
        $this->container->instance('router', $router);
        $this->container->instance('events', $this->events);
        // Share PDO if initialized
        $pdo = null;
        try { $pdo = \App\Core\Database::pdo(); } catch (\Throwable $e) { $pdo = null; }
        if ($pdo) {
            $this->container->instance('db.pdo', $pdo);
        }

        foreach ($this->modules as $module) {
            $module->register($router, $this->container, $this->events);
        }
    }
}
