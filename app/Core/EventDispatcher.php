<?php
namespace App\Core;

class EventDispatcher
{
    private array $listeners = array();

    /**
     * Subscribe to an event name.
     * @param string $event
     * @param callable $listener
     * @return void
     */
    public function on(string $event, callable $listener)
    {
        if (!isset($this->listeners[$event])) {
            $this->listeners[$event] = array();
        }
        $this->listeners[$event][] = $listener;
    }

    /**
     * Remove a previously registered listener.
     * @param string $event
     * @param callable $listener
     * @return void
     */
    public function off(string $event, callable $listener)
    {
        if (!isset($this->listeners[$event])) { return; }
        $this->listeners[$event] = array_filter($this->listeners[$event], function ($l) use ($listener) {
            return $l !== $listener;
        });
    }

    /**
     * Emit an event with optional payload.
     * @param string $event
     * @param mixed $payload
     * @return void
     */
    public function emit(string $event, $payload = null)
    {
        if (!isset($this->listeners[$event])) { return; }
        foreach ($this->listeners[$event] as $listener) {
            call_user_func($listener, $payload, $event, $this);
        }
    }
}
