<?php

/**
 * Sets the error log file location to be the current directory and initializes the default timezone.
 */
ini_set('error_log', __DIR__ . '/../shared/error_log.txt');
date_default_timezone_set('Asia/Tokyo');

/**
 * A class for handling exceptions thrown in the application. 
 */
class ExceptionAndErrorHandler
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

        self::errorLog($exception);
        http_response_code(500);
        echo 'Internal Server Error 500';
    }

    /**
     * Handles a NotFoundException by returning a 404 error response.
     */
    public static function notFound()
    {
        http_response_code(404);

        if (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
            exit(json_encode(['error' => 'Not Found']));
        }

        echo '<h1>Page not found!<h1>';
    }

    /**
     * Writes error messages to the error log file.
     */
    public static function errorLog(Throwable $exception)
    {
        $message = get_class($exception) . ': ' . $exception->getMessage();

        if (isset($_SERVER["REMOTE_ADDR"]) && isset($_SERVER['HTTP_USER_AGENT'])) {
            error_log($message . ': ' . $_SERVER["REMOTE_ADDR"] ?? '' . ': ' . $_SERVER['HTTP_USER_AGENT'] ?? '');
        } else {
            error_log($message);
        }

        if (
            defined(EXCEPTION_HANDLER_DISPLAY_ERRORS)
            && EXCEPTION_HANDLER_DISPLAY_ERRORS === true
        ) {
            echo $message;
        }
    }

    public static function handleError(int $errno, string $errstr, string $errfile, int $errline)
    {
        $errorMessage = "{$errstr} in {$errfile} on line {$errline}";
        error_log($errorMessage, 0);
    }
}
