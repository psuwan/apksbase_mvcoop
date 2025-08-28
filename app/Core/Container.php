<?php
namespace App\Core;

class Container
{
    private $bindings = array();
    private $singletons = array();
    private $instances = array();

    /**
     * Bind a factory for a service id.
     * @param string $id
     * @param callable $factory function(Container $c): mixed
     * @return void
     */
    public function bind($id, $factory)
    {
        $this->bindings[$id] = $factory;
    }

    /**
     * Bind a singleton factory for a service id.
     * @param string $id
     * @param callable $factory
     * @return void
     */
    public function singleton($id, $factory)
    {
        $this->singletons[$id] = $factory;
    }

    /**
     * Register a ready instance under an id.
     * @param string $id
     * @param mixed $instance
     * @return void
     */
    public function instance($id, $instance)
    {
        $this->instances[$id] = $instance;
    }

    /**
     * Resolve a service by id.
     * @param string $id
     * @return mixed|null
     */
    public function get($id)
    {
        if (array_key_exists($id, $this->instances)) {
            return $this->instances[$id];
        }
        if (array_key_exists($id, $this->singletons)) {
            $this->instances[$id] = call_user_func($this->singletons[$id], $this);
            unset($this->singletons[$id]);
            return $this->instances[$id];
        }
        if (array_key_exists($id, $this->bindings)) {
            return call_user_func($this->bindings[$id], $this);
        }
        return null;
    }
}
