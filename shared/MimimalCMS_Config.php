<?php
const URL_ROOT = '';
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
const SESSION_COOKIE_PARAMS = [
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax',
];

// Start session.
session_set_cookie_params(SESSION_COOKIE_PARAMS);
session_name("session");
session_start();

// File validator.
const DEFAULT_MAX_FILE_SIZE = 20480;
const URL_STRING_PATTERN = '/^[a-zA-Z0-9-._~!$&\'()*+,;=:@\/?%]+$/';
const RELATIVE_PATH_PATTERN = '/^(?!(?:f|ht)tps?:\/\/)/i';

date_default_timezone_set('Asia/Tokyo'); // TODO:Local
