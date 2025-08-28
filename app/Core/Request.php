<?php
namespace App\Core;

class Request
{
    public static function fromGlobals(): Request
    {
        $instance = new self();
        $instance->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        // Determine the requested path
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH);
        $path = $path ? $path : '/';

        // Normalize path relative to the directory of the executing script (public/)
        // This allows running from a subdirectory like /htdocs/_mvcoop/public
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $scriptDir = $scriptName !== '' ? str_replace('\\', '/', dirname($scriptName)) : '';
        if ($scriptDir && $scriptDir !== '/' && strpos($path, $scriptDir) === 0) {
            $path = substr($path, strlen($scriptDir));
            if ($path === '') { $path = '/'; }
        }

        // Ensure leading slash and remove trailing slash (except root)
        if ($path === '') { $path = '/'; }
        if ($path[0] !== '/') { $path = '/' . $path; }
        $path = rtrim($path, '/');
        $instance->path = $path ? $path : '/';

        return $instance;
    }

    public function getMethod(): string
    {
        return strtoupper($this->method);
    }

    public function getPath()
    {
        return $this->path;
    }

    private $method;
    private $path;
}
