<?php

declare(strict_types=1);

namespace Kernel;

require_once __DIR__ . '/../../shared/config.php';
require_once __DIR__ . '/../../shared/exceptions.php';

require_once __DIR__ . '/../MimimalCMS_api_interfaces.php';
require_once __DIR__ . '/KernelInterfaces.php';
require_once __DIR__ . '/Validator.php';
require_once __DIR__ . '/Reception.php';
require_once __DIR__ . '/ResponseHandler.php';
require_once __DIR__ . '/Response.php';
require_once __DIR__ . '/View.php';
require_once __DIR__ . '/Cookie.php';

session_set_cookie_params(SESSION_COOKIE_PARAMS);
session_start();
require_once __DIR__ . '/Session.php';

require_once __DIR__ . '/route/Route.php';

require_once __DIR__ . '/routing/RoutingInterfaces.php';
require_once __DIR__ . '/routing/ReceptionInitializer.php';
require_once __DIR__ . '/routing/RequestParser.php';
require_once __DIR__ . '/routing/Routing.php';
require_once __DIR__ . '/routing/ControllerArgumentResolver.php';

use Reception;
use Cookie;

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
