<?php

declare(strict_types=1);

use Kernel\ViewInterface;

/**
 * View class for rendering and displaying templates.
 * 
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
class View implements ViewInterface
{
    public string $renderCache;

    public function __construct(string $renderCache = '')
    {
        $this->renderCache = $renderCache;
    }

    public function render(): void
    {
        echo $this->renderCache;
    }

    public function exists(string $viewTemplateFile): bool
    {
        $viewTemplateFile = "/" . ltrim($viewTemplateFile, "/");
        $filePath = VIEWS_DIR . $viewTemplateFile . '.php';
        return file_exists($filePath);
    }

    public function make(string|View $viewTemplateFile, array|null $valuesArray = null): View
    {
        if ($viewTemplateFile instanceof View) {
            $this->renderCache .= $viewTemplateFile->renderCache;
        } else {
            $this->renderCache .= self::get($viewTemplateFile, $valuesArray);
        }

        return $this;
    }

    public static function get(string $viewTemplateFile, ?array $valuesArray = null): string
    {
        if (is_array($valuesArray)) {
            if (array_values($valuesArray) === $valuesArray) {
                throw new InvalidArgumentException('The passed array must be an associative array or an object.');
            }

            extract(self::sanitizeArray($valuesArray));
        }

        $viewTemplateFile = "/" . ltrim($viewTemplateFile, "/");
        $filePath = VIEWS_DIR . $viewTemplateFile . '.php';
        if (!file_exists($filePath)) {
            throw new InvalidArgumentException('Could not find template file: ' . $filePath);
        }

        ob_start();
        include VIEWS_DIR . $viewTemplateFile . '.php';
        return ob_get_clean();
    }

    /**
     * Sanitizes an array of values recursively to prevent XSS attacks.
     *
     * @param array $array Array of values to sanitize.
     * @return array       The sanitized array.
     */
    private static function sanitizeArray(array $input): array
    {
        $output = [];
        foreach ($input as $key => $value) {
            if (substr((string) $key, 0, 1) === '_') {
                $output[$key] = $value;
                continue;
            }

            if ($value instanceof View) {
                $output[$key] = $value->renderCache;
                continue;
            }

            if (is_array($value)) {
                $output[$key] = self::sanitizeArray($value);
            } elseif (is_object($value)) {
                $output[$key] = self::sanitizeObject($value);
            } elseif (is_string($value)) {
                $output[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            } else {
                $output[$key] = $value;
            }
        }

        return $output;
    }

    /**
     * Sanitizes an object of values recursively to prevent XSS attacks.
     *
     * @param object $array Array of values to sanitize.
     * @return object       The sanitized array.
     */
    private static function sanitizeObject(object $input): object
    {
        $output = new stdClass();
        foreach ($input as $key => $value) {
            if (substr((string) $key, 0, 1) === '_') {
                $output->$key = $value;
                continue;
            }

            if ($value instanceof View) {
                $output->$key = $value->renderCache;
                continue;
            }

            if (is_array($value)) {
                $output->$key = self::sanitizeArray($value);
            } elseif (is_object($value)) {
                $output->$key = self::sanitizeObject($value);
            } elseif (is_string($value)) {
                $output->$key = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            } else {
                $output->$key = $value;
            }
        }

        return $output;
    }
}
