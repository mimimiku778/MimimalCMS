<?php

declare(strict_types=1);

namespace Shadow\Kernel\Dispatcher;

use Shadow\Kernel\RouteClasses\RouteDTO;

class ControllerInvoker implements ControllerInvokerInterface
{
    use TraitGetReflectionMethodArges;

    public function invoke(RouteDTO $routeDto, array $classMap)
    {
        $this->classMap = $classMap;

        $contlollerMethodArgs = $this->getMethodArgs($routeDto->controllerClassName, $routeDto->methodName);
        $contloller = new $routeDto->controllerClassName;

        $routeDto->contlollerResponse = $contloller->{$routeDto->methodName}(...$contlollerMethodArgs);
    }
}
