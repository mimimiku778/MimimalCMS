<?php

/**
 * Sets the error log file location to be the current directory and initializes the default timezone.
 */
ini_set('error_log', __DIR__ . '/../shared/error_log.txt');
date_default_timezone_set('Asia/Tokyo');

/**
 * A class for handling exceptions thrown in the application. 
 */
class ExceptionHandler
{
    /**
     * Handles the specified Throwable instance.
     */
    public static function handleException(Throwable $exception)
    {
        if ($exception instanceof NotFoundException) {
            self::notFound();
            return;
        }

        error_log(get_class($exception) . ': ' . $exception->getMessage());
        http_response_code(500);
        echo 'Internal Server Error 500';
    }

    /**
     * Handles a NotFoundException by returning a 404 error response.
     */
    private static function notFound()
    {
        http_response_code(404);

        if (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
            exit(json_encode(['error' => 'Not Found']));
        }

        echo '<h1>Page not found!<h1>';
    }
}
