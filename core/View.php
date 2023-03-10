<?php

/**
 * View class for rendering and displaying templates.
 */
class View
{
    /**
     * Rendered content cache.
     *
     * @var string
     */
    private static string $renderCache = '';

    /**
     * Render a template file with optional values.
     *
     * @param string $viewFile Path to the template file.
     * @param array|null $values [optional] values to pass to the template.
     * @throws LogicException If rendering fails.
     */
    public static function render(string $viewFile, ?array $values = null): void
    {
        if ($values !== null) {
            // Sanitize values to prevent XSS attacks.
            extract(self::sanitizeArray($values));
        }

        // Start output buffering.
        ob_start();

        // Include the template file.
        include __DIR__ . '/../views/' . $viewFile . '.php';

        // Get the content of the buffer and clear it.
        $content = ob_get_clean();

        if ($content === false) {
            // Throw an exception if rendering failed.
            throw new LogicException("Render failed: {$viewFile}");
        }

        // Cache the rendered content.
        self::$renderCache .= $content;
    }

    /**
     * Display the cached content.
     */
    public static function display(): void
    {
        echo self::$renderCache;
    }

    /**
     * Sanitize an array of values recursively to prevent XSS attacks.
     *
     * @param array $array Array of values to sanitize.
     * @return array The sanitized array.
     */
    private static function sanitizeArray(array $array): array
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                // Recursively sanitize sub-arrays.
                $array[$key] = self::sanitizeArray($value);
            } else {
                // Sanitize string values.
                $array[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            }
        }
        return $array;
    }
}
