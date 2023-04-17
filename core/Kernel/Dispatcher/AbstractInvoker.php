<?php

declare(strict_types=1);

namespace Shadow\Kernel\Dispatcher;

use Shadow\Kernel\Reception;

abstract class AbstractInvoker
{
    protected ConstructorInjectionInterface $ci;

    public function __construct(?ConstructorInjectionInterface $ci = null)
    {
        $this->ci = $ci ?? new ConstructorInjection;
    }

    /**
     * Get the arguments for the given closure function and return an array of both the closure arguments and validated input data.
     */
    protected function getMethodArgs(string $className, string $methodName): array
    {
        $reflectionMethod = new \ReflectionMethod($className, $methodName);
        if (!$reflectionMethod->isPublic()) {
            throw new \RuntimeException('Method is private');
        }

        $methodArgs = [];
        foreach ($reflectionMethod->getParameters() as $param) {
            $paramType = $param->getType();

            if ($paramType === null || $paramType->isBuiltin()) {
                $methodArgs[] = Reception::$inputData[$param->name] ?? null;
                continue;
            }

            $paramClassName = $paramType->getName();
            if (!class_exists($paramClassName)) {
                throw new \InvalidArgumentException('Class not found');
            }

            $methodArgs[] = $this->ci->constructorInjection($paramClassName);
        }

        return $methodArgs;
    }

    /**
     * Get the arguments for the given closure function and return an array of both the closure arguments and validated input data.
     */
    protected function getClosureArgs(\Closure $closure): array
    {
        $reflection = new \ReflectionFunction($closure);

        $closureArgs = [];
        $validArray = [];
        foreach ($reflection->getParameters() as $param) {
            $paramType = $param->getType();

            if ($paramType === null || $paramType->isBuiltin()) {
                $closureArgs[] = Reception::$inputData[$param->name] ?? null;
                $validArray[] = Reception::$inputData[$param->name] ?? null;;
                continue;
            }

            $paramClassName = $paramType->getName();
            if (!class_exists($paramClassName)) {
                throw new \InvalidArgumentException('Class not found');
            }

            $closureArgs[] = $this->ci->constructorInjection($paramClassName);
        }

        return [$closureArgs, $validArray];
    }
}