<?php

namespace Shadow\Kernel\Dispatcher;

interface ConstructorInjectionInterface
{
    /**
     * Resolves a class constructor's dependencies recursively through constructor injection.
     *
     * @param string $className          The name of the class to resolve
     * @param array  &$resolvedInstances Instances that have already been resolved
     * 
     * @return mixed The resolved instance
     * 
     * @throws \ReflectionException
     */
    public function constructorInjection(string $className, array &$resolvedInstances = []);
}
