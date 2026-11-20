<?php
// ==============================================================================
// Smart Gram - Simple, Robust Request Router
// ==============================================================================

namespace App\Core;

class Router {
    private array $routes = [];

    public function get(string $path, array $handler, array $middlewares = []): void {
        $this->addRoute('GET', $path, $handler, $middlewares);
    }

    public function post(string $path, array $handler, array $middlewares = []): void {
        $this->addRoute('POST', $path, $handler, $middlewares);
    }

    private function addRoute(string $method, string $path, array $handler, array $middlewares): void {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => '/' . trim($path, '/'),
            'controller' => $handler[0],
            'action' => $handler[1],
            'middlewares' => $middlewares
        ];
    }

    /**
     * Dispatch the current HTTP request to matching controller action
     */
    public function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

        // Normalize base URL path if Smart Gram is placed in a subdirectory (e.g. /smart-gram/public)
        $scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        if ($scriptName !== '/' && $scriptName !== '' && str_starts_with($uri, $scriptName)) {
            $uri = substr($uri, strlen($scriptName));
        }
        $uri = '/' . trim($uri, '/');

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $uri) {
                // Execute Middlewares
                foreach ($route['middlewares'] as $mw) {
                    $this->runMiddleware($mw);
                }

                // If POST request, validate CSRF automatically if token is sent or required
                if ($method === 'POST') {
                    $token = $_POST['_csrf_token'] ?? $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);
                    if (!$token) {
                        $rawBody = file_get_contents('php://input');
                        if (!empty($rawBody)) {
                            $decoded = json_decode($rawBody, true);
                            $token = $decoded['_csrf_token'] ?? $decoded['csrf_token'] ?? null;
                        }
                    }

                    if (!Csrf::validate($token)) {
                        http_response_code(403);
                        if (str_starts_with($uri, '/api/')) {
                            header('Content-Type: application/json; charset=utf-8');
                            echo json_encode(['success' => false, 'error' => 'CSRF_INVALID', 'message' => 'Invalid or missing CSRF token']);
                            return;
                        }
                        die("<div style='font-family:sans-serif; padding:20px; background:#f8d7da; color:#721c24; border-radius:6px; margin:30px;'>
                            <h3>403 Forbidden - Security Token Mismatch</h3>
                            <p>Your session has expired or the submission was invalid. Please refresh the page and try again.</p>
                            <a href='javascript:history.back()'>&larr; Go Back</a>
                        </div>");
                    }
                }

                $controllerClass = $route['controller'];
                $action = $route['action'];

                $controller = new $controllerClass();
                $controller->$action();
                return;
            }
        }

        // 404 Not Found
        http_response_code(404);
        view('layouts/404', ['title' => '404 - Page Not Found']);
    }

    private function runMiddleware(string $mw): void {
        Session::start();
        $user = Session::get('user');

        if ($mw === 'auth') {
            if (!$user) {
                Session::setFlash('error', 'Please login to access the internal portal.');
                redirect('login');
            }
        } elseif ($mw === 'guest') {
            if ($user) {
                redirect('dashboard');
            }
        } elseif (str_starts_with($mw, 'role:')) {
            $allowedRoles = explode(',', substr($mw, 5));
            if (!$user || !in_array($user['role'], $allowedRoles, true)) {
                http_response_code(403);
                die("<div style='font-family:sans-serif; padding:20px; background:#f8d7da; color:#721c24; border-radius:6px; margin:30px;'>
                    <h3>403 Access Denied</h3>
                    <p>You do not have permission to access this internal module.</p>
                    <a href='" . url('dashboard') . "'>&larr; Return to Dashboard</a>
                </div>");
            }
        }
    }
}
