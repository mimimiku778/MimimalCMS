<?php

declare(strict_types=1);

/**
 * Class Route - Handles routing of incoming requests
 * 
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
class Route
{
    // Whether request content type is JSON
    private bool $isJson;

    /**
     * Route constructor.
     * Parses incoming request URI and calls corresponding controller method
     */
    public function __construct()
    {
        // Check if the request content type is JSON
        $this->isJson = strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false;

        // Get request URI
        $requestUri = strtolower(strtok($_SERVER['REQUEST_URI'] ?? '/', '?'));

        // Parse request URI
        [$controllerClassName, $methodName] = $this->parseRequestUri($requestUri);

        // Execute controller method
        $this->runControllerMethod($controllerClassName, $methodName);
    }

    /**
     * Parses incoming request URI to determine which controller method to call
     *
     * @param string $requestUri Request URI to parse
     * @return array Array containing controller class name and method name
     */
    private function parseRequestUri(string $requestUri): array
    {
        // Split request URI by "/"
        $path = explode('/', $requestUri);

        // If there is a 3rd path, return 404 error
        if (isset($path[3]) && $path[3]) {
            $this->showError();
        }

        // If path contains invalid characters, return 404 error
        if (preg_grep('/[^a-z0-9_]/', array_slice($path, 1))) {
            $this->showError();
        }

        // Set default controller name
        $controllerClassName = $this->isJson ? 'IndexApiController' : 'IndexPageController';

        // Resolve controller name
        if (!empty($path[1])) {
            $controllerPrefix = ucfirst($path[1]);
            $controllerSuffix = $this->isJson ? 'ApiController' : 'PageController';
            $controllerClassName = $controllerPrefix . $controllerSuffix;
        }

        // Resolve method name
        if (isset($path[2]) && $path[2]) {
            $methodName = $path[2];
        } else {
            // Use "index" if second path is empty
            $methodName = 'index';
        }

        // Return controller name and method name
        return [$controllerClassName, $methodName];
    }

    /**
     * Calls the specified controller method
     *
     * @param string $controllerClassName Name of the controller class
     * @param string $methodName Name of the method to call
     */
    private function runControllerMethod(string $controllerClassName, string $methodName)
    {
        // Resolve controller file path
        $controllerDir = $this->isJson ? 'api' : 'pages';
        $controllerFilePath = __DIR__ . "/../controllers/{$controllerDir}/{$controllerClassName}.php";

        // Return 404 error if controller file does not exist
        if (!file_exists($controllerFilePath)) {
            $this->showError();
        }

        // Load controller
        require $controllerFilePath;
        $controller = new $controllerClassName();

        // Return 404 error if method does not exist
        if (!method_exists($controller, $methodName)) {
            $this->showError();
        }

        // Execute controller method
        $controller->$methodName();
    }

    /**
     * Displays 404 error
     */
    private function showError()
    {
        http_response_code(404);

        if ($this->isJson) {
            exit(json_encode(['error' => 'Not Found']));
        } else {
            // Show 404 error page
            exit('<h1>Page not found!<h1>');
        }
    }
}
