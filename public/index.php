<?php

/**
 * MimimalCMS 0.1
 * 
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Shadow\Kernel\Route;
use App\Middleware\VerifyCsrfToken;

Route::path('image/store@post')
    ->matchFile('file', IMAGE_MIME_TYPE, emptyAble: false)
    ->matchStr('imageType', regex: '/(JPG|PNG|WEBP)/')
    ->matchNum('imageSize', min: 0, max: 1000)
    ->fails(redirect('image'));

Route::run(VerifyCsrfToken::class);
