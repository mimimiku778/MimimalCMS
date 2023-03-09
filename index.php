<?php

/**
 * MimimalCMS
 * 
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */

// Display exceptions on/off.
const EXCEPTION_HANDLER_DISPLAY_ERRORS = true;


require_once __DIR__ . '/shared/config.php';
require_once __DIR__ . '/shared/functions.php';
require_once __DIR__ . '/shared/exceptions.php';
require_once __DIR__ . '/core/SimpleAutoloader.php';
require_once __DIR__ . '/core/ExceptionHandler.php';
require_once __DIR__ . '/core/Route.php';

spl_autoload_register('SimpleAutoloader::load');
set_exception_handler('ExceptionHandler::handleException');

// Get any path as a GET value by passing a path with placeholders as an array.
// Example: ['user/{id}', 'user/{id}/image', posts/{postId}]
Route::start();
