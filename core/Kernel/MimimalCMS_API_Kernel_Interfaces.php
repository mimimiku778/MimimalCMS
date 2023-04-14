<?php

/**
 * MimimalCMS0.1 APIs
 * 
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */

namespace Shadow\Kernel;

interface RouteFirstInterface
{
    /**
     * Adds a path.
     * 
     * @param string $path The first argument must be a string representing the path.  
     * 
     * * **Optional:** If you want to define the controller(s) explicitly instead of determining them dynamically from the path,
     *     pass them as additional arguments after the path string.  
     *     `['explicitControllerClassName ', 'methodName'] + ['optionalRequestMethod']`  
     * 
     * * **Example:** `path('mypage')` `path('mypage/post@POST@DELETE')` `path('mypage/posts/{post_id}')`  
     *     `path('mypage', [Mypage::class, 'index'])`  
     *     `path('posts/likes/{post_id}@POST@DELETE', [PostLikes::class, 'like', 'POST'], [PostLikes::class, 'disLike', 'DELETE'])`
     * 
     * @return RouteSecondInterface The instance of the Route class, to allow for method chaining.
     * 
     * @throws \InvalidArgumentException
     */
    public static function path(string|array ...$path): RouteSecondInterface;

    /**
     * Runs the application.
     *
     * @param string ...$middlewareName The names of the middleware to run for all routing.
     * @return void
     */
    public static function run();

    /**
     * Adds the specified middleware group to all routes chained after this method.
     *
     * @param string ...$name The names of the middleware group to add.
     *
     * @return \Shadow\Kernel\RouteClasses\RouteMiddlewareGroupInterface The middleware group instance, to allow for method chaining.
     */
    public static function middlewareGroup(string ...$name): \Shadow\Kernel\RouteClasses\RouteMiddlewareGroupInterface;
}

interface RouteSecondInterface
{
    /**
     * Adds a validator to the route validator array.
     * 
     * * **Example:** `Route::path('home/{pageId}')->match(fn (string $pageId): bool => ctype_digit($pageId));`
     * 
     * @param \Closure $validator     The validator to be added.
     * @param string   $requestMethod [optional] Specify the request method to validate.
     * 
     * @return static The instance of the Route class.
     */
    public function match(\Closure $callback, ?string $requestMethod = null): static;

    /**
     * Adds a string validator to the route validator array.
     * 
     * **Example:** `Route::path('search/{keyword}')->matchStr('keyword'));`
     * 
     * @param string            $parametaName  The key of a value. It can be accessed using dot notation.
     * @param string|null       $requestMethod [optional] Specify the request method to validate. If null, applies to all HTTP methods.
     * @param int|null          $maxLen        [optional] The maximum length of the string.
     * @param string|array|null $regex         [optional] Specifies a regular expression pattern that the input string must match.
     *                                         If an array of strings is provided instead, a regular expression pattern will be generated from the array elements.
     * @param bool|null         $emptyAble     [optional] If the string can be empty or not.
     * @param string|null       $e             [optional] An Exception name to be thrown if validation fails.
     * 
     * @return static                    The instance of the Route class, to allow for method chaining.
     * 
     * @throws LogicException            If an error occurred in preg_match.
     * 
     * @throws InvalidArgumentException  If the elements of the `$regex` array contain non-strings.
     * 
     * @throws ValidationException       If the input string is invalid (not a string), does not match the specified regex pattern,
     *                                   or is empty when not allowed.  
     *                                   * Error codes:  
     *                                   1001 - The input must be a string.  
     *                                   1002 - The input string does not match the specified regex pattern.  
     *                                   1003 - The input string contains only whitespace characters or an empty string.  
     *                                   1004 - The input string exceeds the maximum length limit of {maxLen} characters.  
     */
    public function matchStr(
        string $parametaName,
        ?string $requestMethod = null,
        ?int $maxLen = null,
        string|array|null $regex = null,
        ?bool $emptyAble = false
    ): static;

    /**
     *  **Example:** `Route::path('home/{pageId}')->matchNum('pageId', min:1));`
     * 
     * @param string      $parametaName  The key of a value. It can be accessed using dot notation.
     * @param string|null $requestMethod [optional] Specify the request method to validate. If null, applies to all HTTP methods.
     * @param int|null    $max           [optional] The maximum numeric value.
     * @param int|null    $min           [optional] The minimum numeric value.
     * @param int|null    $exactMatch    [optional] The numeric value for exact match.
     * @param string|null $e             [optional] An Exception name to be thrown if validation fails.
     * 
     * @return static                    The instance of the Route class, to allow for method chaining.
     * 
     * @throws ValidationException       If the input fails validation.
     *                                   * Error codes:  
     *                                   2001 - The input must be an integer or a string containing only digits.  
     *                                   2002 - The input does not match the expected value.  
     *                                   2003 - The input must be greater than or equal to [min].  
     *                                   2004 - The input must be less than or equal to [max].  
     */
    public function matchNum(
        string $parametaName,
        ?string $requestMethod = null,
        ?int $max = null,
        ?int $min = null,
        ?int $exactMatch = null
    ): static;

    /**
     * Validates a file uploaded via HTTP request based on various criteria.
     *
     * @param string      $parametaName     The name of the file parameter in the HTTP request.
     * @param array       $allowedMimeTypes Array of allowed mime types for the file.
     *                                      * **Example:** `['image/jpeg', 'image/png', 'image/gif', 'image/webp']`
     * 
     * @param int         $maxFileSize      Maximum allowed file size in kilobytes (KB).
     * @param bool|null   $emptyAble        Whether an empty file is allowed or not. Defaults to true.
     * @param string|null $requestMethod    The HTTP request method. If null, applies to all HTTP methods.
     *
     * @return static  The instance of the class.
     *
     * @throws LogicException      If the file is not uploaded via HTTP POST.
     * 
     * @throws ValidationException If the file is too large, has an extension not allowed,
     *                             or has a mime type that does not match the file type.  
     *                             * Error codes:  
     *                             3001 - File too large.  
     *                             3002 - File extension not allowed.  
     *                             3003 - File type does not match.  
     */
    public function matchFile(
        string $parametaName,
        array $allowedMimeTypes,
        int $maxFileSize = DEFAULT_MAX_FILE_SIZE,
        ?bool $emptyAble = true,
        ?string $requestMethod = null,
    ): static;

    /**
     * Adds the specified middleware to the route.
     *
     * @param array       $name          The names of the middleware to add.
     * @param string|null $requestMethod The HTTP request method for which the middleware should be applied. 
     *                                   If null, the middleware will be applied to all request methods.
     *
     * @return static                    This Route instance, to allow for method chaining.
     */
    public function middleware(array $name, ?string $requestMethod = null): static;

    /**
     * Defines what to do when validation fails.
     * Captures `\ValidationException` and saves the errors to the session's "Errors".
     * Takes a callback function as its argument, which is called when a redirect is required.
     * Only the `\redirect()` helper function can be passed as an argument.
     * 
     * @param ResponseInterface $redirect The callback function to call when a redirect is required.
     * @param string|null       $requestMethod The HTTP request method. If null, applies to all HTTP methods.
     * 
     * @return static The Route instance, to allow for method chaining.
     */
    public function fails(ResponseInterface $redirect, ?string $requestMethod = null): static;
}

interface ReceptionInterface
{
    /**
     * Get the value of a request input parameter by name.
     *
     * @param  string|null  $name    The key of a value. It can be accessed using dot notation.
     * @param  mixed        $default
     * @return mixed
     */
    public static function input(string $name = null, mixed $default = null): mixed;

    /**
     * Check if a specific input field exists.
     *
     * @param string $name The name of the input field to check.
     * @return bool        Returns true if the input field exists, otherwise returns false.
     */
    public static function has(string $name): bool;

    /**
     * Returns an object version of the input data.
     * 
     * @param string|null $name The name of the property to retrieve as an object. It can be accessed using dot notation.  
     *                          If null, the entire input data is returned as an object.
     * @return \stdClass|null
     */
    public static function getObject(string $name = null): \stdClass|null;

    /**
     * Overwrites the input data with the given data.
     *
     * @param array $data The data to overwrite the input data with.
     */
    public static function overWrite(array $data);

    /**
     * Determine if the request Content-Type is JSON.
     *
     * @return bool
     */
    public static function isJson(): bool;

    /**
     * Checks if the HTTP request method matches the given method.
     *
     * @param string $requestMethod The HTTP request method to check (e.g. "GET", "POST", etc.).
     * @return bool                 True if the HTTP request method matches the given method, false otherwise.
     */
    public static function method(): string;

    /**
     * Returns the HTTP request method.
     *
     * @return string The HTTP request method (e.g. "GET", "POST", etc.).
     */
    public static function isMethod(string $requestMethod): bool;
}

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
     * @param string|\View $viewTemplateFile Path to the template file.
     * @param array|null $valuesArray        [optional] associative array of values to pass to the template, 
     *                                       Keys starting with "_" will not be sanitized.
     * @throws \InvalidArgumentException     If passed invalid array.
     */
    public function make(string|\View $viewTemplateFile, array|null $valuesArray = null): \View;

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
}

interface ResponseInterface
{

    /**
     * Sets a flash session value or values by key.
     * If the given key is an array, it merges it with the existing flash session.
     *
     * @param string|array       $key   The key or array of keys to set in the flash session.
     * @param mixed     　　    　$value The value to set.
     * @return ResponseInterface        This instance for method chaining.
     */
    public function with(string|array $key, mixed $value = null): ResponseInterface;

    /**
     * Adds an error message to the session's error array.
     * If the given key is an array, it merges it with the existing errors array.
     *
     * @param string|array $key     The key or array of keys to add to the errors array.
     * @param int          $code    The error code.
     * @param string       $message The error message.
     */
    public function withErrors(string $key, int $code = 0, string $message = ''): ResponseInterface;

    /**
     * Save input values to session, excluding specified names (if provided).
     * 
     * @param string ...$exceptNames The names of form inputs to exclude from being flashed.
     */
    public function withInput(string ...$exceptNames): ResponseInterface;


    /**
     * Returns HTTP status code and response.
     */
    public function send();
}

interface SessionInterface
{
    /**
     * Adds a value to the session as a flash message.
     *
     * Flash messages are only available for one request, and are then deleted.
     *
     * @param string|array $key   The key to add the value to or an associative array of key-value pairs.
     * @param mixed        $value [optional] The value to add to the session.
     */
    public static function flash(string|array $key, mixed $value = null);

    /**
     * Pushes one or multiple values to the session.
     *
     * @param string|array $key   The key to add the value to or an associative array of key-value pairs.
     * @param mixed        $value [optional] The value to add to the session.
     */
    public static function push(string|array $key, mixed $value = null);

    /**
     * Gets a value from the session and/or flash session.
     *
     * @param string $key The key of the value to get.
     * 
     * @return mixed|null The value if found, null otherwise.
     */
    public static function get(string $key, mixed $default = null): mixed;

    /**
     * Removes a value from the session and/or flash session.
     *
     * @param string $key The key of the value to remove.
     * 
     * @return bool       True if the value was found and removed, false otherwise.
     */
    public static function remove(string $key): bool;

    /**
     * Clears the session and flash session.
     */
    public static function flush();

    /**
     * Check if a key exists in any session.
     *
     * @param string $key The key to check.
     * 
     * @return bool       True if the key exists in any session, false otherwise.
     */
    public static function has(string $key): bool;

    /**
     * Adds an error message to the session's error array.
     *
     * @param string    $key     The key
     * @param int|mixed $code    [optional] The error code.
     * @param string    $message [optional] The error message.
     */
    public static function addError(string $key, int $code = 0, string $message = '');

    /**
     * Gets the error message value from the session's error array by key.
     *
     * @param string|null $key The key of the error message value to retrieve. If null, all errors are returned.
     * 
     * @return array The error value if found, an empty array otherwise.
     *               If $key is null, an associative array of error keys and values is returned.
     *               Otherwise, an array with 'code' and 'message' keys, representing the error code and message, respectively,
     *               is returned for the specified key.
     * 
     *               * **Example for a specific key:** `['code' => 1001, 'message' => 'The input must be a string.']`
     *               * **Example for all errors:** `['key1' => ['code' => 1001, 'message' => 'Error message 1'], 'key2' => ['code' => 1002, 'message' => 'Error message 2']]`
     */
    public static function getError(string $key = null): array;

    /**
     * Gets the error message string from the session's error array by key.
     *
     * @param string $key The key of the error message value to retrieve.
     * 
     * @return string|null The error message string if found, null otherwise.
     */
    public static function getErrorMessage(string $key): ?string;

    /**
     * Gets the error code value from the session's error array by key.
     *
     * @param string $key The key of the error message value to retrieve.
     * 
     * @return int|null The error code value if found, null otherwise.
     */
    public static function getErrorCode(string $key): ?int;

    /**
     * Checks if an error message exists in the session's error array by key.
     *
     * @param string|null $key The key of the error message to check.
     * @return bool            True if the error message exists, false otherwise.
     */
    public static function hasError(string $key): bool;

    /**
     * Save input values to session, excluding specified names (if provided).
     * 
     * @param string ...$exceptNames The names of form inputs to exclude from being flashed.
     */
    public static function flashInput(string ...$exceptNames);
}

interface CookieInterface
{
    /**
     * Pushes one or multiple values to the cookie.
     *
     * @param string|array $key   The key to add the value to or an associative array of key-value pairs.
     * @param mixed        $value [optional] The value to add to the cookie.
     * @return void
     */
    public static function push(
        string|array $key,
        mixed $value = null,
        int $expires = 0,
        string $path = '/',
        string $samesite = COOKIE_DEFAULT_SAMESITE,
        bool $secure = COOKIE_DEFAULT_SECURE,
        bool $httpOnly = COOKIE_DEFAULT_HTTPONLY,
        string $domain = ''
    );

    /**
     * Gets a value from the cookie.
     *
     * @param string $key The key of the value to get.
     * @return mixed|null The value if found, null otherwise.
     */
    public static function get(string $key, mixed $default = null): mixed;

    /**
     * Removes a value from the cookie.
     *
     * @param string $key The key of the value to remove.
     * @return bool       True if the value was found and removed, false otherwise.
     */
    public static function remove(string $key): bool;

    /**
     * Clears all cookies.
     *
     * @return void
     */
    public static function flush();

    /**
     * Check if a key exists in any cookie.
     *
     * @param string $key The key to check.
     * @return bool       True if the key exists in any cookie, false otherwise.
     */
    public static function has(string $key): bool;


    /**
     * Generate a CSRF token and store it in the session and cookie.
     */
    public static function csrfToken();

    /**
     * Refresh CSRF token in the session and cookie.
     */
    public static function refreshCsrfToken();
}

interface ValidatorInterface
{
    const ZERO_WHITE_SPACE = '/[\x{200B}-\x{200D}\x{FEFF}]/u';

    /**
     * Validates a stirng and returns true if it meets the given criteria.
     * 
     * @param mixed $string        The input value to validate.function
     * @param int|null $maxLen     [optional] The maximum length of the string.
     * @param string|null $regex   [optional] If specified, the input string must match this regex pattern.
     * @param bool|null $emptyAble [optional] If the string can be empty or not.
     * @param string|null $e       [optional] An Exception name to be thrown if validation fails.
     * 
     * @return string|false        True if the input is valid, otherwise false.
     * 
     * @throws LogicException      If an error occurred in preg_match.
     * 
     * @throws Throwable           If the input string is invalid (not a string), does not match the specified regex pattern,
     *                             or is empty when not allowed.  
     *                             * Error codes:  
     *                             1001 - The input must be a string.  
     *                             1002 - The input string does not match the specified regex pattern.  
     *                             1003 - The input string contains only whitespace characters or an empty string.  
     *                             1004 - The input string exceeds the maximum length limit of {maxLen} characters.  
     */
    public static function str(
        mixed $input,
        ?int $maxLen = null,
        ?string $regex = null,
        ?bool $empty = true,
        ?string $e = null
    ): string|false;

    /**
     * Validate whether the specified key exists in the array and meets the specified string conditions.
     * 
     * @param array $array         The array to be validated.
     * @param string $key The      key to be validated.
     * @param int|null $maxLen     [optional] The maximum length of the string.
     * @param string|null $regex   [optional] If specified, the input string must match this regex pattern.
     * @param bool|null $emptyAble [optional] If the string can be empty or not.
     * @param string|null $e       [optional] An Exception name to be thrown if validation fails.
     * 
     * @return string|false        True if the input is valid, otherwise false.
     * 
     * @throws LogicException      If an error occurred in preg_match.
     * 
     * @throws Throwable           If the key exists but validation fails and an exception is specified.valid
     *                             * Error codes:  
     *                             1001 - The input must be a string.  
     *                             1002 - The input string does not match the specified regex pattern.  
     *                             1003 - The input string contains only whitespace characters or an empty string.  
     *                             1004 - The input string exceeds the maximum length limit of {maxLen} characters.  
     */
    public static function arrayStr(
        array $array,
        string $key,
        ?int $maxLen = null,
        ?string $regex = null,
        ?bool $empty = true,
        ?string $e = null
    ): string|false;

    /**
     * Validates a number and returns true if it meets the given criteria.
     *
     * @param mixed  $input        The input value to validate.
     * @param int|null $max        [optional] The maximum numeric value.
     * @param int|null $min        [optional] The minimum numeric value.
     * @param int|null $exactMatch [optional] The numeric value for exact match.
     * @param string|null $e       [optional] An Exception name to be thrown if validation fails.
     *
     * @return int|false           True if the input is valid, otherwise false.
     * 
     * @throws Throwable           If the input fails validation.
     *                             * Error codes:  
     *                             2001 - The input must be an integer or a string containing only digits.  
     *                             2002 - The input does not match the expected value.  
     *                             2003 - The input must be greater than or equal to [min].  
     *                             2004 - The input must be less than or equal to [max].  
     */
    public static function num(
        mixed $input,
        ?int $max = null,
        ?int $min = null,
        ?int $exactMatch = null,
        ?string $e = null
    ): int|false;

    /**
     * Validate whether the specified key exists in the array and meets the specified numeric conditions.
     *
     * @param array $array         The array to be validated
     * @param string $key          The key to be validated
     * @param int|null $max        [optional] The maximum numeric value.
     * @param int|null $min        [optional] The minimum numeric value.
     * @param int|null $exactMatch [optional] The numeric value for exact match.
     * @param string|null $e       [optional] An Exception name to be thrown if validation fails.
     * 
     * @return int|false           True if the input is valid, otherwise false.
     * 
     * @throws Throwable           If the key exists but validation fails and an exception is specified.
     *                             * Error codes:  
     *                             2001 - The input must be an integer or a string containing only digits.  
     *                             2002 - The input does not match the expected value.  
     *                             2003 - The input must be greater than or equal to [min].  
     *                             2004 - The input must be less than or equal to [max].  
     */
    public static function arrayNum(
        array $array,
        string $key,
        ?int $max = null,
        ?int $min = null,
        ?int $exactMatch = null,
        ?string $e = null
    ): int|false;

    /**
     * Validates an uploaded file based on the allowed mime types and maximum file size.
     * 
     * @param array  $file             Array of the file input element.
     * @param array  $allowedMimeTypes Array of allowed mime types for the file.
     * @param int    $maxFileSize      Maximum allowed file size in kilobytes (KB).
     * 
     * @return array Returns an array containing information about the uploaded file if it passes validation.
     * 
     * @throws LogicException      If the file is not uploaded via HTTP POST.
     * 
     * @throws ValidationException If the file is too large, has an extension not allowed,
     *                             or has a mime type that does not match the file type.  
     *                             * Error codes:  
     *                             3001 - File too large.  
     *                             3002 - File extension not allowed.  
     *                             3003 - File type does not match.  
     */
    public static function file(array $file, array $allowedMimeTypes, int $maxFileSize = DEFAULT_MAX_FILE_SIZE): array;
}