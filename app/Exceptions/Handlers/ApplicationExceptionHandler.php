<?php

namespace App\Exceptions\Handlers;

class ApplicationExceptionHandler implements ApplicationExceptionHandlerInterface
{
    public function handleException(\Throwable $e)
    {
        echo $e->__toString();
    }
}