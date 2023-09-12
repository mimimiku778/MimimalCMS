<?php

namespace App\Services;

require_once __DIR__ . '/../../vendor/autoload.php';

class TestService
{
    public int $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}

app()->bind(TestService::class, function () {
    return new TestService(777);
});

$test = app(TestService::class);
var_dump($test);
