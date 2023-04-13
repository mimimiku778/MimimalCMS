<?php

require_once __DIR__ . '/../shared/config.php';

require_once __DIR__ . '/ExceptionHandler.php';
set_exception_handler('ExceptionHandler::handleException');

error_reporting(E_ALL);

set_error_handler(function ($no, $msg, $file, $line) {
    if (error_reporting() !== 0) {
        throw new ErrorException($msg, 0, $no, $file, $line);
    }
});

register_shutdown_function(function () {
    $last = error_get_last();
    if (
        isset($last['type'])
        && boolval($last['type'] & (E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR))
    ) {
        ExceptionHandler::handleException(
            new ErrorException($last['message'], 0, $last['type'], $last['file'], $last['line'])
        );
    }
});

require_once __DIR__ . '/SimpleAutoloader.php';
spl_autoload_register('SimpleAutoloader::load');

require_once __DIR__ . '/MimimalCMS_helper_functions.php';

require_once __DIR__ . '/kernel/Kernel.php';
