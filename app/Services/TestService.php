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

class TestService2
{
    public int $value2;
    public TestService $test;

    public function __construct(int $value2, TestService $test)
    {
        $this->value2 = $value2;
        $this->test = $test;
    }

    public function getValue2(): int
    {
        return $this->test->value * $this->value2;
    }
}

app()->bind(TestService::class, function () {
    return new TestService(100);
});

app()->bind('テスト', TestService2::class);

$test = app('テスト', ['value2' => 500]);

echo $test->getValue2();
