<?php

declare(strict_types=1);

namespace Kernel;

/**
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
class RouteMiddlewareGroupSecond extends RouteSecond implements RouteMiddlewareGroupSecondInterface
{
    use TraitMiddlewarePath;

    public function __construct(RouteDTO &$routeDto)
    {
        $this->routeDto = $routeDto;
    }
}
