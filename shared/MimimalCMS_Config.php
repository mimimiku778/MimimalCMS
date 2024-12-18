<?php

/**
 * MimimalCMS v1
 * 
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */

const PUBLIC_DIR = __DIR__ . '/../public';
const VIEWS_DIR = __DIR__ . '/../app/Views';
const JSON_STORAGE_DIR =  __DIR__ . '/../storage/json';
const CONFIG_JSON_FILE_PATH = __DIR__ . '/../app/Config/ConfigJson.json';

// Default options for cookies.
const COOKIE_DEFAULT_SECURE = false;
const COOKIE_DEFAULT_HTTPONLY = true;
const COOKIE_DEFAULT_SAMESITE = 'lax';

// Options for session.
const FLASH_SESSION_KEY_NAME = 'mimimalFlashSession';
const SESSION_KEY_NAME = 'mimimalSession';

// File validator.
const DEFAULT_MAX_FILE_SIZE = 20480;
const URL_STRING_PATTERN = '/^[a-zA-Z0-9-._~!$&\'()*+,;=:@\/?%]+$/';
const RELATIVE_PATH_PATTERN = '/^(?!(?:f|ht)tps?:\/\/)/i';

date_default_timezone_set('Asia/Tokyo');
session_start();

// Default URL root.
const URL_ROOT = '';
/* 
if (preg_match("{^/en.*}", $_SERVER['REQUEST_URI'] ?? '')) {
    define('URL_ROOT', '/en');
    define('PUBLIC_DIR', __DIR__ . '/../public/en');
    define('VIEWS_DIR', __DIR__ . '/../app/Views/en');
    define('JSON_STORAGE_DIR', __DIR__ . '/../storage/json/en');
    define('CONFIG_JSON_FILE_PATH', __DIR__ . '/../app/Config/en/ConfigJson.json');
} else {
    define('URL_ROOT', '');
}
 */

// Default options for session cookies.
/* 
if (($_SERVER['HTTP_HOST'] ?? '') === 'example.me') {
    $_SERVER['HTTPS'] = 'on';
    define('SESSION_COOKIE_PARAMS', [
        'secure' => true,
        'httponly' => true,
        'samesite' => 'lax',
    ]);

    define('COOKIE_DEFAULT_SECURE', true);
} else {
    define('SESSION_COOKIE_PARAMS', [
        'secure' => false,
        'httponly' => true,
        'samesite' => 'lax',
    ]);

    define('COOKIE_DEFAULT_SECURE', false);
}
 */