<?php

namespace Shadow\Kernel\Dispatcher;

interface ControllerArgumentResolverInterface
{
    public function getControllerArgs(): array;
}
