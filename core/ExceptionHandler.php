<?php

/**
 * A class for handling exceptions thrown in the application. 
 * 
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
class ExceptionHandler
{
    const ERROR_MESSAGES = [
        BadRequestException::class =>       ['httpCode' => 400, 'httpStatusMessage' => 'Bad Request'],
        ValidationException::class =>       ['httpCode' => 400, 'httpStatusMessage' => 'Bad Request'],
        InvalidInputException::class =>     ['httpCode' => 400, 'httpStatusMessage' => 'Bad Request'],
        SessionTimeoutException::class =>   ['httpCode' => 401, 'httpStatusMessage' => 'Unauthorized'],
        UnauthorizedException::class =>     ['httpCode' => 401, 'httpStatusMessage' => 'Unauthorized'],
        NotFoundException::class =>         ['httpCode' => 404, 'httpStatusMessage' => 'Not Found'],
        MethodNotAllowedException::class => ['httpCode' => 405, 'httpStatusMessage' => 'Method Not Allowed'],
        ThrottleRequestsException::class => ['httpCode' => 429, 'httpStatusMessage' => 'Too Many Requests'],
    ];

    /**
     * Handles the specified Throwable instance.
     */
    public static function handleException(Throwable $e)
    {
        $flagName = 'EXCEPTION_HANDLER_DISPLAY_BEFORE_OB_CLEAN';
        $obCleanFlag = defined($flagName) && constant($flagName);
        if ($obCleanFlag) {
            ob_clean();
        }

        if ($e instanceof TestException) {
            self::errorResponse($e, 'please try again later', 500, 'Internal Server Error😥');
            return;
        }

        if (!array_key_exists(get_class($e), self::ERROR_MESSAGES)) {
            self::errorResponse($e, 'please try again later', 500, 'Internal Server Error😥');
            self::errorLog($e);
            return;
        }

        $error = self::ERROR_MESSAGES[get_class($e)];

        if ($error['httpCode'] === 404 || $error['httpCode'] === 405) {
            self::errorResponse($e, '', ...$error);
            return;
        }

        self::errorResponse($e, mb_convert_encoding($e->getMessage(), 'UTF-8'), ...$error);
        self::errorLog($e);
    }

    private static function errorResponse(
        Throwable $e,
        string $message,
        int $httpCode,
        string $httpStatusMessage,
    ) {
        http_response_code($httpCode);

        $flagName = 'EXCEPTION_HANDLER_DISPLAY_ERROR_DETAILS';
        $showDetailsFlag = defined($flagName) && constant($flagName);

        if (self::isJsonRequest()) {
            self::jsonResponse([
                'error' => [
                    'code' => $e->getCode(),
                    'message' => $showDetailsFlag ? self::getDetailsMessage($e) : $message
                ]
            ]);

            return;
        }

        $detailsMessage = $showDetailsFlag ? (get_class($e) . ": " . self::getDetailsMessage($e)) : '';
        $detailsMessage = htmlspecialchars($detailsMessage, ENT_QUOTES, 'UTF-8');

        if (!self::showErrorPage($httpCode, $httpStatusMessage, $detailsMessage)) {
            $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
            echo "{$httpCode} {$httpStatusMessage}<br>{$message}";
            echo "<pre>" . $detailsMessage . "</pre>";
        }
    }

    private static function getDetailsMessage(Throwable $e): string
    {
        return mb_convert_encoding($e->getMessage(), 'UTF-8')
            . " in "
            . $e->getFile() . '(' . $e->getLine() . ')'
            . ": \n"
            . $e->getTraceAsString();
    }

    private static function showErrorPage(int $httpCode, string $httpStatusMessage, string $detailsMessage): bool
    {
        $filePath = __DIR__ . '/../views/errors/' . $httpCode . '.php';
        if (file_exists($filePath)) {
            require_once $filePath;
            return true;
        }

        $filePath = __DIR__ . '/../views/errors/error.php';
        if (file_exists($filePath)) {
            require_once $filePath;
            return true;
        }

        return false;
    }

    /**
     * Writes error messages to the error log file and exits.
     * 
     * @param Throwable $e
     */
    public static function errorLog(Throwable $e)
    {
        $time = date('Y-m-d H:i:s') . ' ' . date_default_timezone_get() . ': ';

        $message = get_class($e) . ': ' . $e->getMessage() . ": \n" . $e->getTraceAsString();

        $getHeaderString = function ($key, $val) {
            if (!is_string($val)) {
                $val = var_export($val, true);
            }

            $val = str_replace("\n", '', $val);

            return "{$key}: {$val}";
        };

        $headerString = array_map($getHeaderString, array_keys($_SERVER), $_SERVER);
        $requestHeader = implode("\n", $headerString);

        $flagName = 'EXCEPTION_LOG_DIRECTORY';
        $flag = defined($flagName) && constant($flagName);
        if ($flag && is_string($dir = EXCEPTION_LOG_DIRECTORY) && is_writable($dir)) {
            error_log($time . "\n" . $message . $requestHeader . "\n" . "\n", 3, $dir);
        }
    }

    private static function jsonResponse(array $data)
    {
        header("Content-Type: application/json; charset=utf-8");
        ob_start('ob_gzhandler');
        echo json_encode($data);
    }

    private static function isJsonRequest(): bool
    {
        return strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false;
    }
}
