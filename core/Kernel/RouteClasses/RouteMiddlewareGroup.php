<?php

declare(strict_types=1);

namespace Kernel;

/**
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
class RouteMiddlewareGroup extends AbstractRoute implements RouteMiddlewareGroupInterface
{
    use TraitMiddlewarePath;

    public function __construct(RouteDTO &$routeDto, array $middlewareGroup)
    {
        $this->routeDto = $routeDto;
        self::$middlewareGroup = $middlewareGroup;
    }
}
