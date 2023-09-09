<?php

namespace Shadow\Kernel;

interface ApplicationInterface
{
    public function make(string $abstract): object;

    public function singleton(string $className, ?\Closure $concrete = null): void;
}
