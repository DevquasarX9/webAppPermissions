<?php

namespace App;

use App\Config\App;

class Router
{
    private array $routes = [];
    private string $requestMethod;
    private string $requestUri;

    public function __construct()
    {
        $this->requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    }

    public function get(string $path, callable $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, callable $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, callable $handler): void
    {
        $this->addRoute('PUT', $path, $handler);
    }

    public function delete(string $path, callable $handler): void
    {
        $this->addRoute('DELETE', $path, $handler);
    }

    public function dispatch(): void
    {
        $method = $this->requestMethod;
        $uri = $this->requestUri;

        // Check for exact match
        if (isset($this->routes[$method][$uri])) {
            $handler = $this->routes[$method][$uri];
            $this->executeHandler($handler);

            return;
        }

        // Check for pattern matches (simple parameter matching)
        foreach ($this->routes[$method] ?? [] as $pattern => $handler) {
            $regex = $this->patternToRegex($pattern);

            if (preg_match($regex, $uri, $matches)) {
                array_shift($matches); // Remove full match
                $this->executeHandler($handler, $matches);

                return;
            }
        }

        // No route found
        $this->notFound();
    }

    public function getRequestMethod(): string
    {
        return $this->requestMethod;
    }

    public function getRequestUri(): string
    {
        return $this->requestUri;
    }

    private function addRoute(string $method, string $path, callable $handler): void
    {
        $this->routes[$method][$path] = $handler;
    }

    private function patternToRegex(string $pattern): string
    {
        // Convert route patterns like /user/{id} to regex
        $pattern = preg_replace('/\{(\w+)\}/', '([^/]+)', $pattern);

        return '#^' . $pattern . '$#';
    }

    private function executeHandler(callable $handler, array $params = []): void
    {
        try {
            $result = call_user_func_array($handler, $params);

            if (is_array($result)) {
                $this->jsonResponse($result);
            } elseif (is_string($result)) {
                echo $result;
            }
        } catch (\Exception $e) {
            if (App::isDebug()) {
                $this->jsonResponse([
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ], 500);
            } else {
                $this->jsonResponse(['error' => 'Internal server error'], 500);
            }
        }
    }

    /**
     * @throws \JsonException
     */
    private function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_THROW_ON_ERROR);
    }

    private function notFound(): void
    {
        http_response_code(404);
        require __DIR__ . '/Views/404.php';
    }
}
