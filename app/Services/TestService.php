<?php

namespace App\Services;

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
