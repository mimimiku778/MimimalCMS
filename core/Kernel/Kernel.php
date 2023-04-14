<?php

declare(strict_types=1);

namespace Shadow\Kernel;

use Shadow\Kernel\RouteClasses\RouteDTO;
use Shadow\Kernel\Dispatcher\ReceptionInitializer;
use Shadow\Kernel\Dispatcher\ReceptionInitializerInterface;
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
    private RouteDTO $routeDTO;
    private mixed $contlollerResponse;

    public function __construct(RouteDTO $routeDTO)
    {
        $this->routeDTO = $routeDTO;
    }

    public function handle()
    {
        $this->routing();
        $this->validateRequest();
        $this->callMiddleware();
        $this->callController();
        $this->handleResponse();
    }

    /**
     * @throws \NotFoundException
     * @throws \MethodNotAllowedException
     */
    private function routing()
    {
        $request = new RequestParser;
        $request->parse($this->routeDTO, $_SERVER['REQUEST_URI'] ?? '');

        $routing = new Routing;
        $routing->setRouteDto($this->routeDTO);
        $routing->validatePath();
        $routing->resolveController();
        $routing->validateAllowedMethods();
    }

    /**
     * @throws \NotFoundException        If the request is GET
     * @throws \ValidationException      If the request is other than GET
     * @throws \InvalidArgumentException
     */
    private function validateRequest()
    {
        $reception = new ReceptionInitializer;
        $reception->init($this->routeDTO);
        $reception->callRequestValidator();
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function callMiddleware()
    {
        if (empty($this->routeDTO->getMiddleware())) {
            return;
        }

        $middleware = new MiddlewareInvoker;
        $middleware->invoke($this->routeDTO);
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function callController()
    {
        $controller = new ControllerInvoker;
        $this->contlollerResponse = $controller->invoke($this->routeDTO);
    }

    /**
     * @throws \NotFoundException        If the request is GET
     * @throws \BadRequestException      If the request is other than GET
     */
    private function handleResponse()
    {
        $response = new ResponseHandler;
        $response->handleResponse($this->contlollerResponse);
    }
}
