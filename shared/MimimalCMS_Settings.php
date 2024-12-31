<?php

/**
 * MimimalCMS v1
 * 
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */

use Shared\MimimalCmsConfig;

date_default_timezone_set('Asia/Tokyo');
session_start();

$httpHost = 'example.me';

if (
    ($_SERVER['HTTP_HOST'] ?? '') === $httpHost
    || ($_SERVER['HTTPS'] ?? '') === 'on'
) {
    $_SERVER['HTTPS'] = 'on';
    MimimalCmsConfig::$cookieDefaultSecure = true;
} else {
    MimimalCmsConfig::$cookieDefaultSecure = false;
}
