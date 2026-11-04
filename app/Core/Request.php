<?php

namespace App\Core;

class Request
{
    private array $get;
    private array $post;
    private array $files;
    private array $server;
    private array $headers;
    private ?array $jsonBody = null;

    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->files = $_FILES;
        $this->server = $_SERVER;
        $this->headers = $this->parseHeaders();
        $this->parseJson();
    }

    private function parseHeaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $name = str_replace('_', '-', strtolower(substr($key, 5)));
                $headers[$name] = $value;
            } elseif (in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH'])) {
                $name = str_replace('_', '-', strtolower($key));
                $headers[$name] = $value;
            }
        }
        return $headers;
    }

    private function parseJson(): void
    {
        $contentType = $this->header('content-type', '');
        if (str_contains($contentType, 'application/json')) {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            if (is_array($data)) {
                $this->jsonBody = $data;
            }
        }
    }

    public function method(): string
    {
        $method = strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
        if ($method === 'POST') {
            if (isset($this->post['_method'])) {
                return strtoupper($this->post['_method']);
            }
            if (isset($this->headers['x-http-method-override'])) {
                return strtoupper($this->headers['x-http-method-override']);
            }
        }
        return $method;
    }

    public function path(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';

        // Support subfolder installation
        $scriptName = dirname($this->server['SCRIPT_NAME'] ?? '');
        if ($scriptName !== '/' && $scriptName !== '\\' && str_starts_with($path, $scriptName)) {
            $path = substr($path, strlen($scriptName));
        }

        $path = '/' . trim($path, '/');
        return $path === '' ? '/' : $path;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        if ($this->jsonBody !== null && array_key_exists($key, $this->jsonBody)) {
            return $this->jsonBody[$key];
        }
        if (array_key_exists($key, $this->post)) {
            return $this->post[$key];
        }
        if (array_key_exists($key, $this->get)) {
            return $this->get[$key];
        }
        return $default;
    }

    public function all(): array
    {
        return array_merge($this->get, $this->post, $this->jsonBody ?? []);
    }

    public function file(string $key): ?array
    {
        if (isset($this->files[$key]) && $this->files[$key]['error'] !== UPLOAD_ERR_NO_FILE) {
            return $this->files[$key];
        }
        return null;
    }

    public function hasFile(string $key): bool
    {
        return isset($this->files[$key]) && $this->files[$key]['error'] === UPLOAD_ERR_OK;
    }

    public function header(string $key, mixed $default = null): mixed
    {
        $key = strtolower($key);
        return $this->headers[$key] ?? $default;
    }

    public function isAjax(): bool
    {
        return ($this->header('x-requested-with') === 'XMLHttpRequest') ||
               str_contains($this->header('accept', ''), 'application/json');
    }

    public function ip(): string
    {
        return $this->server['HTTP_X_FORWARDED_FOR'] ?? $this->server['REMOTE_ADDR'] ?? '127.0.0.1';
    }
}
