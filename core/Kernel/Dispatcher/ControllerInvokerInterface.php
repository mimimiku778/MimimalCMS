<?php

namespace Shadow\Kernel\Dispatcher;

interface ControllerInvokerInterface
{
    /**
     * Call the controller method.
     * 
     * @return mixed Response
     */
    public function Invoke(): mixed;
}