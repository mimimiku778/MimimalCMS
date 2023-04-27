<?php

namespace Shadow\Kernel;

interface ViewInterface
{
    /**
     * Display the cached content.
     */
    public function render(): void;

    /**
     * Check if a view template file exists.
     *
     * @param string $viewTemplateFile Path of the view template file to check.
     * @return bool                    Returns true if the file exists, false otherwise.
     */
    public function exists(string $viewTemplateFile): bool;

    /**
     * Render a template file with optional values.
     *
     * @param string|ViewInterface $viewTemplateFile Path to the template file.
     * @param array|null $valuesArray        [optional] associative array of values to pass to the template, 
     *                                       Keys starting with "_" will not be sanitized.
     * @throws \InvalidArgumentException     If passed invalid array.
     */
    public function make(string|ViewInterface $viewTemplateFile, array|null $valuesArray = null): ViewInterface;

    /**
     * Gets rendered template as a string.
     *
     * @param string            $viewTemplateFile Path to the template file.
     * @param array|object|null $valuesArray      Optional associative array of values to pass to the template, 
     *                                            Keys starting with "_" will not be sanitized.
     * @return string                             The rendered template as a string.
     * @throws \InvalidArgumentException          If passed invalid array or template file.
     */
    public static function get(string $viewTemplateFile, ?array $valuesArray = null): string;

    public static function sanitizeArray(array $input): array;
}