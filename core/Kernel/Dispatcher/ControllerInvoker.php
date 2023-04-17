<?php

declare(strict_types=1);

namespace Shadow\Kernel\Dispatcher;

use Shadow\Kernel\RouteClasses\RouteDTO;

class ControllerInvoker extends AbstractInvoker implements ClassInvokerInterface
{
    public function invoke(RouteDTO $routeDto)
    {
        $contlollerMethodArgs = $this->getMethodArgs($routeDto->controllerClassName, $routeDto->methodName);
        $contloller = new $routeDto->controllerClassName;

        $routeDto->contlollerResponse = $contloller->{$routeDto->methodName}(...$contlollerMethodArgs);
    }
}
