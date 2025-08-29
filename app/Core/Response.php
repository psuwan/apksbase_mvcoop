<?php
namespace App\Core;

class Response
{
    /** @var int */
    protected int $status = 200;
    /** @var array<string,string> */
    protected array $headers = array();
    /** @var string */
    protected string $body = '';

    public function __construct(string $body = '', int $status = 200, array $headers = array())
    {
        $this->body = $body;
        $this->status = $status;
        foreach ($headers as $name => $value) {
            $this->headers[$name] = (string)$value;
        }
    }

    /** Set HTTP status code */
    public function status(int $code): self
    {
        $this->status = $code;
        return $this;
        }

    /** Add/replace a header */
    public function withHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /** Replace response body */
    public function withBody(string $body): self
    {
        $this->body = $body;
        return $this;
    }

    /** Output headers and body */
    public function send(): void
    {
        if (!headers_sent()) {
            http_response_code($this->status);
            foreach ($this->headers as $name => $value) {
                header($name . ': ' . $value);
            }
        }
        echo $this->body;
    }

    /** Create a JSON response */
    public static function json($data, int $status = 200, array $headers = array()): self
    {
        $body = json_encode($data);
        if ($body === false) { $body = 'null'; }
        $headers = array('Content-Type' => 'application/json; charset=utf-8') + $headers;
        return new self($body, $status, $headers);
    }

    /** Create a text/plain response */
    public static function text(string $text, int $status = 200, array $headers = array()): self
    {
        $headers = array('Content-Type' => 'text/plain; charset=utf-8') + $headers;
        return new self($text, $status, $headers);
    }

    /** Create an HTML response */
    public static function html(string $html, int $status = 200, array $headers = array()): self
    {
        $headers = array('Content-Type' => 'text/html; charset=utf-8') + $headers;
        return new self($html, $status, $headers);
    }

    /** Create a redirect response */
    public static function redirect(string $location, int $status = 302, array $headers = array()): self
    {
        $headers = array('Location' => $location) + $headers;
        return new self('', $status, $headers);
    }
}
