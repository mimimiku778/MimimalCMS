<?php

namespace App\Exceptions\Handlers;

class ApplicationExceptionHandler implements AppExceptionHandlerInterface
{
    public function handleException(\Throwable $e)
    {
        echo $e->__toString();
    }
}