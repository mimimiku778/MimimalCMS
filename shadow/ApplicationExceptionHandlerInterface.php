<?php

namespace Shadow;

interface ApplicationExceptionHandlerInterface
{
    /**
     * Handles the specified \Throwable instance.
     *
     * @param \Throwable $e The \Throwable instance to handle.
     * @param string $className Throwed exception class name.
     */
    public static function handleException(\Throwable $e, string $className);
}
