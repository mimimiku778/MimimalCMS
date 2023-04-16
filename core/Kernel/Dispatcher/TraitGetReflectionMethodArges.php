<?php

declare(strict_types=1);

namespace Shadow\Kernel\Dispatcher;

use Shadow\Kernel\Reception;

trait TraitGetReflectionMethodArges
{
    /**
     * Get the arguments for the given closure function and return an array of both the closure arguments and validated input data.
     */
    private function getMethodArgs(string $className, string $methodName): array
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

            $methodArgs[] = $this->constructorInjection($paramClassName);
        }

        return $methodArgs;
    }

    /**
     * Constructor injection method using reflection
     *
     * @param string $className         The name of the class to instantiate
     * @param array  $resolvedInstances The already resolved instances
     * @return object                   The instantiated object with injected dependencies
     * @throws \LogicException
     */
    public function constructorInjection(string $className, array &$resolvedInstances = [])
    {
        if (isset($resolvedInstances[$className])) {
            return $resolvedInstances[$className];
        }

        $reflectionClass = new \ReflectionClass($className);
        $constructor = $reflectionClass->getConstructor();
        if ($constructor === null) {
            return new $className();
        }

        $methodArgs = [];
        foreach ($constructor->getParameters() as $param) {
            $paramType = $param->getType();

            if ($paramType === null || $paramType->isBuiltin()) {
                continue;
            }

            $paramClassName = $paramType->getName();
            if (!class_exists($paramClassName)) {
                throw new \InvalidArgumentException('Class not found');
            }

            $methodArgs[] = $this->constructorInjection($paramClassName, $resolvedInstances);
        }

        $resolvedInstances[$className] = $reflectionClass->newInstanceArgs($methodArgs);
        return $resolvedInstances[$className];
    }
}