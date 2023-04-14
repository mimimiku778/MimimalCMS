<?php

declare(strict_types=1);

namespace Shadow\Kernel;

use BadRequestException;
use NotFoundException;

/**
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
class ResponseHandler implements ResponseHandlerInterface
{
    public mixed $response;

    public function __construct(mixed $response)
    {
        $this->response = $response;
    }

    public function handleResponse(): mixed
    {
        if ($this->response instanceof \View) {
            $this->response->render();
            return true;
        }

        if ($this->response instanceof Response) {
            $this->response->send();
            return true;
        }

        if ($this->response instanceof \Closure) {
            ($this->response)();
            return true;
        }

        if ($this->response === false) {
            if (($_SERVER['REQUEST_METHOD'] ?? '') === 'GET') {
                throw new NotFoundException('no response');
            }

            throw new BadRequestException('no response');
        }

        if (is_string($this->response)) {
            echo htmlspecialchars($this->response, ENT_QUOTES, 'UTF-8');
            return true;
        }

        return $this->response;
    }
}
