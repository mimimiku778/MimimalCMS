<?php

declare(strict_types=1);

namespace Kernel;

/**
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
trait TraitPath
{
    protected function addPath(string|array ...$path)
    {
        if (!is_string($path[0] ?? null)) {
            throw new \InvalidArgumentException('The first argument must be a string representing the path.');
        }

        if ($path[0] === '/') {
            $path[0] = '';
        }

        $this->routeDto->routePathArray[] = $path[0];
        if (count($path) === 1) {
            return $this;
        }

        unset($path[0]);
        $key = array_key_last($this->routeDto->routePathArray);
        foreach ($path as $controller) {
            if (!is_array($controller) || !isset($controller[0], $controller[1])) {
                throw new \InvalidArgumentException(
                    'The second argument and later must be an array with two or more string elements that include a controller class name and an action method name.'
                );
            }

            $requestMethod = $controller[2] ?? $this->routeDto->requestMethod;
            $this->routeDto->routeExplicitControllerArray[$key][$requestMethod] = [$controller[0], $controller[1]];
        }
    }
}

trait TraitMiddlewarePath
{
    use TraitPath;

    protected static array $middlewareGroup;

    public function path(string|array ...$path): RouteMiddlewareGroupSecondInterface
    {
        $this->addPath(...$path);

        [$key, $requestMethod] = $this->createArrayKey(null);
        $this->routeDto->routeMiddlewareArray[$key][$requestMethod] = self::$middlewareGroup;

        return new RouteMiddlewareGroupSecond($this->routeDto);
    }
}
