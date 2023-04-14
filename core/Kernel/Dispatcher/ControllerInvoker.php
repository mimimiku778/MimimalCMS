<?php

declare(strict_types=1);

namespace Shadow\Kernel\Dispatcher;

use Shadow\Kernel\RouteClasses\RouteDTO;

class ControllerInvoker implements ControllerInvokerInterface
{
    use TraitGetReflectionMethodArges;

    public function Invoke(RouteDTO $routeDto): mixed
    {
        $contlollerMethodArgs = $this->getMethodArgs($routeDto->controllerClassName, $routeDto->methodName);
        $contloller = new $routeDto->controllerClassName;
        return $contloller->{$routeDto->methodName}(...$contlollerMethodArgs);
    }
}
