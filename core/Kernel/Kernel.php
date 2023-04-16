<?php

declare(strict_types=1);

namespace Shadow\Kernel;

use Shadow\Kernel\RouteClasses\RouteDTO;
use Shadow\Kernel\Dispatcher\ReceptionInitializer;
use Shadow\Kernel\Dispatcher\RequestParser;
use Shadow\Kernel\Dispatcher\Routing;
use Shadow\Kernel\Dispatcher\ControllerInvoker;
use Shadow\Kernel\Dispatcher\MiddlewareInvoker;

/**
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
class Kernel
{
    private array $classMap;
    private RouteDTO $routeDto;

    public function handle(RouteDTO $routeDto)
    {
        $this->classMap = require_once(CLASS_MAP_DIR);
        $this->routeDto = $routeDto;
        $this->parseRequest();
        $this->routing();
        $this->validateRequest();
        $this->callMiddleware();
        $this->callController();
        $this->handleResponse();
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \LogicException
     */
    private function parseRequest()
    {
        $request = new RequestParser;
        $request->parse($this->routeDto, $_SERVER['REQUEST_URI'] ?? '');
    }

    /**
     * @throws NotFoundException
     * @throws MethodNotAllowedException
     */
    private function routing()
    {
        $routing = new Routing;
        $routing->setRouteDto($this->routeDto);
        $routing->validatePath();
        $routing->resolveController();
        $routing->validateAllowedMethods();
    }

    /**
     * @throws NotFoundException        If the request is GET
     * @throws ValidationException      If the request is other than GET
     * @throws \InvalidArgumentException
     */
    private function validateRequest()
    {
        $reception = new ReceptionInitializer;
        $reception->init($this->routeDto);
        $reception->callRequestValidator();
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function callMiddleware()
    {
        if (!$this->routeDto->existsMiddleware()) {
            return;
        }

        $middleware = new MiddlewareInvoker;
        $middleware->invoke($this->routeDto, $this->classMap);
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function callController()
    {
        $controller = new ControllerInvoker;
        $controller->invoke($this->routeDto, $this->classMap);
    }

    /**
     * @throws NotFoundException        If the request is GET
     * @throws BadRequestException      If the request is other than GET
     */
    private function handleResponse()
    {
        $response = new ResponseHandler;
        $response->handleResponse($this->routeDto->contlollerResponse);
    }
}
