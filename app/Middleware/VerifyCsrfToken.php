<?php

declare(strict_types=1);

namespace App\Middleware;

use Shadow\Kernel\Reception;
use Shadow\Kernel\Cookie;

class VerifyCsrfToken
{
    public function handle()
    {
        if (Reception::$requestMethod === 'GET') {
            Cookie::csrfToken();
        }

        if (Reception::$requestMethod !== 'GET') {
            verifyCsrfToken();
        }
    }
}