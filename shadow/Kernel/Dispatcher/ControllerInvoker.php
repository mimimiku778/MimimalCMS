<?php

declare(strict_types=1);

namespace Shadow\Kernel\Dispatcher;

use Shadow\Kernel\RouteClasses\RouteDTO;
use Shared\Exceptions\NotFoundException;

/**
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
class ControllerInvoker extends AbstractInvoker implements ClassInvokerInterface
{
    public function invoke(RouteDTO $routeDto)
    {
        try {
            $contlollerMethodArgs = $this->getMethodArgs($routeDto->controllerClassName, $routeDto->methodName);
        } catch (\BadMethodCallException $e) {
            throw new NotFoundException($e->getMessage());
        }

        $contloller = $this->ci->constructorInjection($routeDto->controllerClassName);
        $routeDto->contlollerResponse = $contloller->{$routeDto->methodName}(...$contlollerMethodArgs);
    }
}
