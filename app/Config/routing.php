<?php

namespace App\Config;

use Shadow\Kernel\Route;
use App\Middleware\VerifyCsrfToken;

Route::path('image/store@post')
    ->matchFile('file', ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], emptyAble: false)
    ->matchStr('imageType', regex: '/(jpeg|png|webp)/')
    ->matchNum('imageSize', min: 0, max: 1000)
    ->fails(redirect('image'));

Route::run(VerifyCsrfToken::class);
