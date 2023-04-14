<?php

declare(strict_types=1);

namespace Shadow\Kernel;

use Shadow\Kernel\RouteClasses\RouteDTO;
use Shadow\Kernel\Dispatcher\ReceptionInitializer;
use Shadow\Kernel\Dispatcher\ReceptionInitializerInterface;
use Shadow\Kernel\Dispatcher\RequestParser;
use Shadow\Kernel\Dispatcher\Routing;
use Shadow\Kernel\Dispatcher\ControllerArgumentResolver;

/**
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
class Kernel
{
    private RouteDTO $routeDTO;
    private ReceptionInitializerInterface $reception;
    private mixed $contlollerResponse;

    public function __construct(RouteDTO $routeDTO)
    {
        $this->routeDTO = $routeDTO;
    }

    public function handle()
    {
        $this->routing();
        $this->validateRequest();
        $this->middleware();
        $this->callController();
        $this->resoponse();
    }

    /**
     * @throws \NotFoundException
     * @throws \MethodNotAllowedException
     */
    private function routing()
    {
        $request = new RequestParser($this->routeDTO, $_SERVER['REQUEST_URI'] ?? '');
        $request->parse();

        $routing = new Routing($this->routeDTO);
        $routing->validatePath();
        $routing->resolveController();
        $routing->validateAllowedMethods();

        $this->reception = new ReceptionInitializer($this->routeDTO);
    }

    /**
     * @throws \NotFoundException        If the request is GET
     * @throws \ValidationException      If the request is other than GET
     * @throws \InvalidArgumentException
     */
    private function validateRequest()
    {
        $this->reception->callRequestValidator();
    }

    private function middleware()
    {
        if (Reception::$requestMethod === 'GET') {
            Cookie::csrfToken();
        }

        if (Reception::$requestMethod !== 'GET') {
            verifyCsrfToken();
        }
    }

    private function callController()
    {
        $argsResolver = new ControllerArgumentResolver;
        $contlollerMethodArgs = $argsResolver->getControllerArgs();

        $contloller = new Reception::$controllerClassName;
        $this->contlollerResponse = $contloller->{Reception::$methodName}(...$contlollerMethodArgs);
    }

    private function resoponse()
    {
        $response = new ResponseHandler($this->contlollerResponse);
        $response->handleResponse();
    }
}
