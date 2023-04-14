<?php

declare(strict_types=1);

namespace Shadow\Kernel\RouteClasses;

/**
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
trait TraitMiddlewarePath
{
    use TraitRoutePath;

    protected static array $middlewareGroup;

    public function path(string|array ...$path): RouteMiddlewareGroupSecondInterface
    {
        $this->addPath(...$path);

        [$key, $requestMethod] = $this->createArrayKey(null);
        $this->routeDto->routeMiddlewareArray[$key][$requestMethod] = self::$middlewareGroup;

        return new RouteMiddlewareGroupSecond($this->routeDto);
    }
}