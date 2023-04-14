<?php

namespace Shadow\Kernel\Dispatcher;

use Shadow\Kernel\RouteClasses\RouteDTO;

interface ControllerInvokerInterface
{
    /**
     * Call the controller method.
     * 
     * @return mixed Response
     */
    public function Invoke(RouteDTO $routeDto): mixed;
}