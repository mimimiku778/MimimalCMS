<?php
// Display exceptions
const EXCEPTION_HANDLER_DISPLAY_ERROR_DETAILS = true;
const EXCEPTION_HANDLER_DISPLAY_BEFORE_OB_CLEAN = true;

const EXCEPTION_HANDLER_DISPLAY_HIDE_DRECTORY = '/var/www/mimikyu.info';
const EXCEPTION_HANDLER_DISPLAY_DOCUMENT_ROOT_NAME = 'mimikyu.info';
const EXCEPTION_HANDLER_DISPLAY_GITHUB_URL = 'https://github.com/mimimiku778/MimimalCMS-v0.1/blob/master/';

const VIEWS_DIR = __DIR__ . '/../views';

// Default options for cookies.
const COOKIE_DEFAULT_SECURE = true;
const COOKIE_DEFAULT_HTTPONLY = true;
const COOKIE_DEFAULT_SAMESITE = 'lax';

// Options for session.
const FLASH_SESSION_KEY_NAME = 'mimimalFlashSession';
const SESSION_KEY_NAME = 'mimimalSession';
const SESSION_COOKIE_PARAMS = [
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax',
];

// Exceptions Log directory.
const EXCEPTION_LOG_DIRECTORY = __DIR__ . '/exception.log';

// Add root directories to search for class files.
const SIMPLE_AUTOLOADER_ROOT_DIRECTORY_NAMES = [
    'controllers',
    'middleware',
    'service',
    'models',
    'models/traits',
    'core',
    'core/kernel',
    'core/storage',
    'controllers/traits',
    'views/classes/',
    'views/classes/traits',
    'controllers/api',
    'controllers/pages',
];

const DEFAULT_MAX_FILE_SIZE = 20480;
const IMAGE_MIME_TYPE = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
