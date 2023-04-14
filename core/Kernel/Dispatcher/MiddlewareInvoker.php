<?php

declare(strict_types=1);

namespace Shadow\Kernel\Dispatcher;

use Shadow\Kernel\Reception;
use Shadow\Kernel\ResponseHandler;
use Shadow\Kernel\RouteClasses\RouteDTO;

class MiddlewareInvoker implements MiddlewareInvokerInterface
{
    use TraitGetReflectionMethodArges;
    use TraitErrorResponse;

    public function Invoke(RouteDTO $routeDto)
    {
        $this->routeFails = $routeDto->getFailsResponse();
        $this->callMiddleware($routeDto);
    }

    private function callMiddleware(RouteDTO $routeDto)
    {
        try {
            foreach ($routeDto->getMiddleware() as $middleware) {
                $className = 'App\\Middleware\\' . $middleware;
                if (!method_exists($className, 'handle')) {
                    throw new \InvalidArgumentException('Could not find: ' . $className . '::handle');
                }

                $methodArgs = $this->getMethodArgs($className, 'handle');

                $instance = new $className;
                $middlewareResponse = $instance->handle(...$methodArgs);

                $handler = new ResponseHandler;
                $response = $handler->handleResponse($middlewareResponse);

                if (is_array($response)) {
                    Reception::$inputData = array_merge(Reception::$inputData, $response);
                }
            }
        } catch (\ValidationException | \NotFoundException | \BadRequestException $e) {
            $this->errorResponse([[
                'key' => $middleware,
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]]);
        }
    }
}
