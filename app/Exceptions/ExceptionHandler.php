<?php

namespace App\Exceptions;

use Shadow\Exceptions\ExceptionHandlerInterface;
use App\Config\AppExceptionHandlerConfig;

class ExceptionHandler implements ExceptionHandlerInterface
{
    const EXCEPTION_MAP = [
        ApplicationException::class => Handlers\ApplicationExceptionHandler::class,
    ];

    public static function handleException(\Throwable $e)
    {
        $handler = self::EXCEPTION_MAP[get_class($e)];
        $handlerInstance = app()->make($handler);
        $handlerInstance->handleException($e);
    }
}
