<?php

namespace App\Exceptions\Handlers;

interface ApplicationExceptionHandlerInterface
{
    /**
     * Handles the specified \Throwable instance.
     *
     * @param \Throwable $e The \Throwable instance to handle.
     */
    public function handleException(\Throwable $e);
}
