<?php

ini_set('error_log', __DIR__ . '/error_log.txt');
date_default_timezone_set('Asia/Tokyo');

class NotFoundException extends Exception
{
}

function exceptionHandler($exception)
{
    if ($exception instanceof NotFoundException) {

        return;
    }

    error_log('Exception: ' . $exception->getMessage());
    http_response_code(500);
    echo 'Internal Server Error 500';
}