<?php

declare(strict_types=1);

namespace Shadow\Kernel\Dispatcher;

use Shadow\Kernel\Reception;

class ControllerInvoker implements ControllerInvokerInterface
{
    use TraitGetReflectionMethodArges;

    public function Invoke(): mixed
    {
        $contlollerMethodArgs = $this->getMethodArgs(Reception::$controllerClassName, Reception::$methodName);
        $contloller = new Reception::$controllerClassName;
        return $contloller->{Reception::$methodName}(...$contlollerMethodArgs);
    }
}
