<?php

namespace App\Config;

use Shadow\Kernel\Route;
use App\Middleware\VerifyCsrfToken;
use App\Middleware\ConfigJsonServiceProvider;

Route::path('image/store@post')
    ->matchFile('file', AppConfig::IMAGE_MIME_TYPE, emptyAble: false)
    ->matchStr('imageType', regex: '/(jpeg|png|webp)/')
    ->matchNum('imageSize', min: 0, max: 1000)
    ->fails(redirect('image'));

Route::run(
    ConfigJsonServiceProvider::class,
    VerifyCsrfToken::class
);