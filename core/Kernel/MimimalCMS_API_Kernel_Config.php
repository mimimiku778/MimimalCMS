<?php
const VIEWS_DIR = __DIR__ . '/../../views';

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
session_set_cookie_params(SESSION_COOKIE_PARAMS);
session_start();

// Add root directories to search for class files.
const SIMPLE_AUTOLOADER_ROOT_DIRECTORY_NAMES = [
    'controllers',
    'middleware',
    'service',
    'models',
    'models/traits',
    'core',
    'core/Kernel',
    'core/Storage',
    'controllers/traits',
    'views/classes/',
    'views/classes/traits',
    'controllers/api',
    'controllers/pages',
];

const DEFAULT_MAX_FILE_SIZE = 20480;
const IMAGE_MIME_TYPE = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
