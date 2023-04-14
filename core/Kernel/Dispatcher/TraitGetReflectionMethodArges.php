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

            if (!class_exists($paramType->getName())) {
                throw new \InvalidArgumentException('Class not found');
            }

            $paramClassName = $paramType->getName();
            $methodArgs[] = new $paramClassName();
        }

        return $methodArgs;
    }
}
