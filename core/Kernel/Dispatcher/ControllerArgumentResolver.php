<?php

declare(strict_types=1);

namespace Shadow\Kernel\Dispatcher;

use Shadow\Kernel\Reception;

/**
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
class ControllerArgumentResolver implements ControllerArgumentResolverInterface
{
    /**
     * @throws \NotFoundException
     */
    public function getControllerArgs(): array
    {
        $reflectionMethod = new \ReflectionMethod(Reception::$controllerClassName, Reception::$methodName);
        if (!$reflectionMethod->isPublic()) {
            throw new \NotFoundException('Controller method is private');
        }

        $methodArgs = [];
        foreach ($reflectionMethod->getParameters() as $param) {
            $paramType = $param->getType();

            if ($paramType === null || $paramType->isBuiltin()) {
                $methodArgs[] = Reception::$inputData[$param->name] ?? null;
                continue;
            }

            if (!class_exists($paramType->getName())) {
                $m = 'Invalid class name: ' . $paramType . ' not found: $' . $param->name;
                throw new \InvalidArgumentException($m);
            }

            $paramClassName = $paramType->getName();
            $methodArgs[] = new $paramClassName();
        }

        return $methodArgs;
    }
}
