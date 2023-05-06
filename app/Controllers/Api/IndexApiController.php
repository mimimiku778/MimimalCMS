<?php

declare(strict_types=1);

namespace App\Controllers\Api;

class IndexApiController
{
    public function index()
    {
        return response(['message' => 'Hello World']);
    }
}
