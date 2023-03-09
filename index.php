<?php

/**
 * MimimalCMS
 * 
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */

require_once __DIR__ . '/shared/config.php';
require_once __DIR__ . '/shared/functions.php';
require_once __DIR__ . '/shared/exceptions.php';
require_once __DIR__ . '/core/ExceptionAndErrorHandler.php';
require_once __DIR__ . '/core/Route.php';

// Add directories to search for class files
const AUTOLOADER_ROOT_DIRECTORY_NAMES = ['core', 'models'];
spl_autoload_register('simpleAutoloader');

// Display errors and exceptions on/off.
const EXCEPTION_HANDLER_DISPLAY_ERRORS = false;
set_exception_handler('ExceptionAndErrorHandler::handleException');
set_error_handler('ExceptionAndErrorHandler::handleError');

// Get any path as a GET value by passing a path with placeholders as an array.
// Example: ['user/{id}', 'user/{id}/image', posts/{postId}]
Route::start();
