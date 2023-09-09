<?php

declare(strict_types=1);

namespace Shadow\Kernel\Dispatcher;

use App\Config\ConstructorInjectionClassMap;

class ConstructorInjection implements ConstructorInjectionInterface
{
    private static array $singletonInstances = [];
    private array $injectionParameters;
    private array $classMap;
    private array $reflectionClasses;

    public function __construct(array $injectionParameters = [])
    {
        $this->classMap = ConstructorInjectionClassMap::$map;
        $this->injectionParameters = $injectionParameters;
    }

    public function constructorInjection(string $className, array &$resolvedInstances = [], $singleton = true): object
    {
        if ($singleton && isset(self::$singletonInstances[$className])) {
            return $this->getSingletonInstance($className);
        }

        if (isset($resolvedInstances[$className])) {
            return $resolvedInstances[$className];
        }

        if (!class_exists($className)) {
            $concreteName = $this->resolveInterfaceToClass($className);
        } else {
            $concreteName = $className;
        }

        $reflectionClass = $this->getReflectionClass($concreteName);
        $constructor = $reflectionClass->getConstructor();

        if ($constructor === null) {
            return new $concreteName();
        }

        $methodArgs = $this->getMethodArgs($constructor, $resolvedInstances);
        $resolvedInstances[$className] = $reflectionClass->newInstanceArgs($methodArgs);

        return $resolvedInstances[$className];
    }

    /**
     * Gets the resolved arguments for a given constructor.
     *
     * @param \ReflectionMethod $constructor The constructor to resolve dependencies for
     * @param array             &$resolvedInstances Instances that have already been resolved
     * 
     * @return array            An array of resolved dependencies for the constructor
     * 
     * @throws \ReflectionException
     */
    private function getMethodArgs(\ReflectionMethod $constructor, array &$resolvedInstances = []): array
    {
        $methodArgs = [];

        foreach ($constructor->getParameters() as $param) {
            $paramType = $param->getType();

            if (isset($this->injectionParameters[$param->name]) || $paramType === null || $paramType->isBuiltin()) {
                $methodArgs[] = $this->injectionParameters[$param->name] ?? null;
                continue;
            }

            $paramClassName = $paramType->getName();

            if (!class_exists($paramClassName)) {
                $paramClassName = $this->resolveInterfaceToClass($paramClassName);
            }

            if (isset(self::$singletonInstances[$paramClassName])) {
                $methodArgs[] = $this->getSingletonInstance($paramClassName);
                continue;
            }

            if (isset($resolvedInstances[$paramClassName])) {
                $methodArgs[] = $resolvedInstances[$paramClassName];
                continue;
            }

            $methodArgs[] = $this->constructorInjection($paramClassName, $resolvedInstances);
        }

        return $methodArgs;
    }

    public function resolveInterfaceToClass(string $interfaceName): string
    {
        if (!isset($this->classMap[$interfaceName])) {
            throw new \LogicException("No implementation found for interface '{$interfaceName}'");
        }

        $className = $this->classMap[$interfaceName];

        if (!class_exists($className)) {
            throw new \LogicException("Class '{$className}' not found");
        }

        return $className;
    }

    /**
     * Gets a ReflectionClass instance for a given class name.
     *
     * @param string $className The name of the class to get a ReflectionClass instance for
     * 
     * @return \ReflectionClass A ReflectionClass instance for the given class
     * 
     * @throws \ReflectionException
     */
    private function getReflectionClass(string $className): \ReflectionClass
    {
        if (!isset($this->reflectionClasses[$className])) {
            $this->reflectionClasses[$className] = new \ReflectionClass($className);
        }

        return $this->reflectionClasses[$className];
    }

    public function registerSingletonInstance(string $className, ?\Closure $concrete = null): void
    {
        self::$singletonInstances[$className] = ['instance' => $concrete, 'flag' => false];
    }

    /**
     * Retrieve a singleton instance from the DI container.
     *
     * @param string $className The service name
     * @return object The singleton instance
     * 
     * @throws LogicException If Closure return value is not an object.
     */
    private function getSingletonInstance(string $className): object
    {
        $element = self::$singletonInstances[$className];

        if ($element['flag']) {
            return $element['instance'];
        }

        if ($element['instance'] instanceof \Closure) {
            $concrete = $element['instance']();
            if (!is_object($concrete)) {
                throw new \LogicException("Closure return value is not an object");
            }

            self::$singletonInstances[$className]['instance'] = $concrete;
            self::$singletonInstances[$className]['flag'] = true;
        } else {
            self::$singletonInstances[$className]['instance'] = $this->constructorInjection($className, singleton: false);
            self::$singletonInstances[$className]['flag'] = true;
        }

        return self::$singletonInstances[$className]['instance'];
    }
}
