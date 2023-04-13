<?php

declare(strict_types=1);

namespace Kernel;

require_once __DIR__ . '/RouteInterfaces.php';
require_once __DIR__ . '/AbstractRoute.php';
require_once __DIR__ . '/RouteTraits.php';

require_once __DIR__ . '/RouteDTO.php';

require_once __DIR__ . '/RouteSecond.php';

require_once __DIR__ . '/RouteMiddlewareGroup.php';
require_once __DIR__ . '/RouteMiddlewareGroupSecond.php';

/**
 * Class Route is used for defining routes and validating parameters.
 * 
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
class Route extends AbstractRoute implements RouteFirstInterface
{
    use TraitPath;

    private static ?Route $instance = null;

    private function __construct()
    {
        $this->routeDto = new RouteDTO;
    }

    private static function getInstance(): Route
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function path(string|array ...$path): RouteSecondInterface
    {
        $instance = self::getInstance();
        $instance->addPath(...$path);
        return new RouteSecond($instance->routeDto);
    }

    public static function run(string ...$middlewareName)
    {
        $instance = self::getInstance();
        $instance->routeDto->kernelMiddlewareArray = $middlewareName;

        (new Kernel($instance->routeDto))->handle();
        exit;
    }

    public static function middlewareGroup(string ...$name): RouteMiddlewareGroupInterface
    {
        $instance = self::getInstance();
        return new RouteMiddlewareGroup($instance->routeDto, $name);
    }
}
