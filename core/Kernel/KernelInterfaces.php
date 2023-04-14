<?php

/**
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */

namespace Shadow\Kernel;

interface ResponseHandlerInterface
{
    public function handleResponse(mixed $response): mixed;
}
