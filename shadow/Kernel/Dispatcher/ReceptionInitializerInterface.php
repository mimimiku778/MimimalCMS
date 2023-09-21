<?php

namespace Shadow\Kernel\Dispatcher;

use Shadow\Kernel\RouteClasses\RouteDTO;

interface ReceptionInitializerInterface
{
    public function init(RouteDTO $routeDto);
    public function callRequestValidator();
}