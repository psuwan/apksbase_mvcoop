<?php
namespace App\Core;

class Router
{
    private array $routes = array();
    private $fallback;

    public function get($path, $handler)
    {
        $this->routes['GET'][$this->normalize($path)] = $handler;
    }

    public function post($path, $handler)
    {
        $this->routes['POST'][$this->normalize($path)] = $handler;
    }

    public function fallback($handler)
    {
        $this->fallback = $handler;
    }

    public function dispatch($request)
    {
        $method = $request->getMethod();
        $path = $this->normalize($request->getPath());
        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            if ($this->fallback) {
                $result = call_user_func($this->fallback);
                $this->output($result);
                return;
            }
            http_response_code(404);
            echo '404 Not Found';
            return;
        }

        if (is_array($handler)) {
            $class = $handler[0];
            $method = $handler[1];
            $instance = new $class();
            $result = call_user_func(array($instance, $method));
            $this->output($result);
            return;
        }

        $result = call_user_func($handler);
        $this->output($result);
    }

    private function output($result): void
    {
        if ($result instanceof Response) {
            $result->send();
            return;
        }
        if ($result !== null) {
            echo $result;
        }
    }

    private function normalize($path): string
    {
        if ($path === '') { return '/'; }
        if ($path[0] !== '/') { $path = '/' . $path; }
        $trimmed = rtrim($path, '/');
        return $trimmed ? $trimmed : '/';
    }
}
