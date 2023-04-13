<?php

/**
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */

namespace Kernel;

interface RequestParserInterface
{
    const AVAILABLE_REQUEST_METHOD = [
        'GET',
        'HEAD',
        'POST',
        'PUT',
        'PATCH',
        'DELETE'
    ];

    public function __construct(RouteDTO &$routeDto, string $requestUri);
    public function parse();
}

interface RoutingInterface
{
    const DEFAULT_CONTROLLER_CLASS_NAME = 'Index';
    const DEFAULT_CONTROLLER_METHOD_NAME = 'index';

    const API_CONTROLLER_SUFFIX = 'ApiController';
    const API_CONTROLLER_DIR = __DIR__ . '/../../../controllers/api';

    const PAGE_CONTROLLER_SUFFIX = 'PageController';
    const PAGE_CONTROLLER_DIR = __DIR__ . '/../../../controllers/pages';

    public function __construct(RouteDTO &$routeDto);
    public function validatePath();
    public function resolveController();
    public function validateAllowedMethods();
}

interface ReceptionInitializerInterface
{
    public function __construct(RouteDTO $routeDto);
    public function callRequestValidator();

    /**
     * Returns the domain and HTTP host of the current request.
     * 
     * @return string The domain and HTTP host of the current request.
     */
    public static function getDomainAndHttpHost(): string;
}

interface ControllerArgumentResolverInterface
{
    public function getControllerArgs(): array;
}
