<?php

namespace App\Core;

class Router
{
    private array $routes = [];
    private array $groupStack = [];

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function group(array $attributes, callable $callback): void
    {
        $this->groupStack[] = $attributes;
        $callback($this);
        array_pop($this->groupStack);
    }

    public function get(string $path, mixed $action, array $middleware = []): self
    {
        return $this->addRoute('GET', $path, $action, $middleware);
    }

    public function post(string $path, mixed $action, array $middleware = []): self
    {
        return $this->addRoute('POST', $path, $action, $middleware);
    }

    public function put(string $path, mixed $action, array $middleware = []): self
    {
        return $this->addRoute('PUT', $path, $action, $middleware);
    }

    public function patch(string $path, mixed $action, array $middleware = []): self
    {
        return $this->addRoute('PATCH', $path, $action, $middleware);
    }

    public function delete(string $path, mixed $action, array $middleware = []): self
    {
        return $this->addRoute('DELETE', $path, $action, $middleware);
    }

    private function addRoute(string $method, string $path, mixed $action, array $middleware = []): self
    {
        $prefix = '';
        $groupMiddleware = [];

        foreach ($this->groupStack as $group) {
            if (isset($group['prefix'])) {
                $prefix .= '/' . trim($group['prefix'], '/');
            }
            if (isset($group['middleware'])) {
                $m = is_array($group['middleware']) ? $group['middleware'] : [$group['middleware']];
                $groupMiddleware = array_merge($groupMiddleware, $m);
            }
        }

        $fullPath = rtrim($prefix, '/') . '/' . ltrim($path, '/');
        $fullPath = '/' . trim($fullPath, '/');
        if ($fullPath === '') {
            $fullPath = '/';
        }

        $allMiddleware = array_merge($groupMiddleware, $middleware);

        // Convert {param} placeholders to named regex groups
        $pattern = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_-]*)\}/', '(?P<$1>[^/]+)', $fullPath);
        $pattern = '#^' . $pattern . '$#';

        $this->routes[] = [
            'method' => $method,
            'path' => $fullPath,
            'pattern' => $pattern,
            'action' => $action,
            'middleware' => $allMiddleware,
        ];

        return $this;
    }

    public function dispatch(Request $request): Response
    {
        $method = $request->method();
        $path = $request->path();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $path, $matches)) {
                // Filter named parameters
                $params = [];
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $params[$key] = urldecode($value);
                    }
                }

                // Execute middleware
                foreach ($route['middleware'] as $mwClass) {
                    $mw = new $mwClass();
                    $result = $mw->handle($request);
                    if ($result instanceof Response) {
                        return $result;
                    }
                }

                // Execute action
                return $this->executeAction($route['action'], $request, $params);
            }
        }

        // 404 Not Found
        if ($request->isAjax()) {
            return Response::json(['error' => 'Resource not found.'], 404);
        }

        return new Response(View::render('public.404', ['title' => 'Page Not Found - Meridian Academy']), 404);
    }

    private function executeAction(mixed $action, Request $request, array $params): Response
    {
        if (is_callable($action)) {
            $result = call_user_func_array($action, array_merge([$request], $params));
        } elseif (is_array($action)) {
            [$controllerClass, $method] = $action;
            $controller = new $controllerClass();
            $result = call_user_func_array([$controller, $method], array_merge([$request], $params));
        } elseif (is_string($action) && strpos($action, '@') !== false) {
            [$controllerClass, $method] = explode('@', $action);
            if (!str_starts_with($controllerClass, 'App\\')) {
                $controllerClass = "App\\Controllers\\{$controllerClass}";
            }
            $controller = new $controllerClass();
            $result = call_user_func_array([$controller, $method], array_merge([$request], $params));
        } else {
            throw new \InvalidArgumentException('Invalid route action handler.');
        }

        if ($result instanceof Response) {
            return $result;
        }

        if (is_array($result) || is_object($result)) {
            return Response::json($result);
        }

        return new Response((string)$result);
    }
}
