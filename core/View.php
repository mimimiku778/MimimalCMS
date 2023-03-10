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
     * @param array|null $values Optional values to pass to the template.
     * @throws LogicException If rendering fails.
     */
    public static function render(string $viewFile, ?array $values = null): void
    {
        if ($values !== null) {
            extract(self::sanitizeArray($values));
        }

        ob_start();
        include __DIR__ . '/../views/' . $viewFile . '.php';
        $content = ob_get_clean();

        if ($content === false) {
            throw new LogicException("Render failed: {$viewFile}");
        }

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
                $array[$key] = self::sanitizeArray($value);
            } else {
                $array[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            }
        }
        return $array;
    }
}
