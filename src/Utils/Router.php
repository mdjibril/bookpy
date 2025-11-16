<?php
namespace App\Utils;

use App\Repositories\BookingRepository;
use App\Repositories\EmailTemplateRepository;
use App\Services\EmailService;
use App\Services\PdfService;

class Router {
    protected array $routes = [];
    protected array $container = [];

    public function __construct(array $container = [])
    {
        $this->container = $container;
    }

    // Remove normalize() calls from get/post methods
    public function get(string $path, $handler): void {
        // Store the path as defined, not normalized
        $this->routes['GET'][$path] = $handler; 
    }

    public function post(string $path, $handler): void {
        // Store the path as defined, not normalized
        $this->routes['POST'][$path] = $handler;
    }

    // Keep the normalize function for the URI only
    protected function normalize(string $p): string {
        $p = rtrim($p, '/');
        return $p === '' ? '/' : $p;
    }

    // In Router.php

    public function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $path = $this->normalize($uri); // This is the incoming URI: /admin/email-templates/edit/7

        if (!isset($this->routes[$method])) {
            // No routes defined for this method, 404
            http_response_code(404);
            echo "404 Not Found";
            return;
        }

        foreach ($this->routes[$method] as $routePath => $handler) {
            // 1. Convert dynamic route definition (e.g., /edit/{id}) into a regex
            // The regex replaces {id} with a capturing group (\d+) or (.+?)
            // We are using a basic capturing group (.+?) for any characters
            $regex = str_replace(['/', '{id}', '{slug}', '{token}'], ['\/', '(.+?)', '(.+?)', '([a-zA-Z0-9]+)'], $routePath);
            $regex = '/^' . $regex . '$/i';

            // 2. Attempt to match the incoming $path against the $regex
            if (preg_match($regex, $path, $matches)) {
                // Match found!
                
                // Extract the dynamic parameters (starting from index 1)
                $params = array_slice($matches, 1);
                
                // Route found, now execute the handler with parameters
                $this->executeHandler($handler, $params);
                return;
            }
        }

        // No matching route found after checking all definitions
        http_response_code(404);
        echo "404 Not Found";
    }

    // Create a new helper method to execute the handler
    protected function executeHandler($handler, array $params = []): void {
        // Callable closure/function
        if (is_callable($handler)) {
            call_user_func_array($handler, $params);
            return;
        }

        // Array handler: [ClassName::class, 'method']
        if (is_array($handler) && is_string($handler[0]) && isset($handler[1])) {
            $controllerClass = $handler[0];
            $method = $handler[1];

            // Basic dependency injection for known services
            if ($controllerClass === \App\Controllers\Admin\AdminController::class) {
                // Inject EmailService into AdminController
                $controller = new $controllerClass(
                    $this->container[EmailService::class],
                    $this->container[BookingRepository::class],
                    $this->container[PdfService::class],
                    $this->container[EmailTemplateRepository::class]
                );
            } elseif ($controllerClass === \App\Controllers\BookingController::class) {
                // Inject services into BookingController
                $controller = new $controllerClass(
                    $this->container[EmailService::class],
                    $this->container[EmailTemplateRepository::class],
                    $this->container[BookingRepository::class]
                );
            } elseif ($controllerClass === \App\Controllers\Admin\EmailTemplateController::class) {
                // Inject repository into EmailTemplateController
                $controller = new $controllerClass(
                    $this->container[EmailTemplateRepository::class]
                );
            } elseif ($controllerClass === \App\Controllers\AvailabilityController::class) {
                // Inject repository into AvailabilityController
                $controller = new $controllerClass(
                    $this->container[BookingRepository::class]
                );
            } else {
                // Other controllers that don't need dependencies yet
                $controller = new $controllerClass();
            }
            
            // Use call_user_func_array to pass parameters to the controller method
            call_user_func_array([$controller, $method], $params);
            return;
        }
        
        // ... You can skip the string handler "Controller@action" logic for simplicity here ...
        
        // Fallback for handler
        http_response_code(500);
        echo "500 Server Error: Invalid route handler";
    }
}