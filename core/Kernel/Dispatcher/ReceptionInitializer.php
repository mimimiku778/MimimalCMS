<?php

declare(strict_types=1);

namespace Shadow\Kernel\Dispatcher;

use Shadow\Exceptions\UploadException;
use Shadow\Kernel\Reception;
use Shadow\Kernel\RouteClasses\RouteDTO;
use Shadow\Exceptions\ValidationException;

/**
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
class ReceptionInitializer implements ReceptionInitializerInterface
{
    use TraitErrorResponse;

    private RouteDTO $routeDto;

    public function init(RouteDTO $routeDto)
    {
        $this->routeDto = $routeDto;
        $this->routeFails = $routeDto->getFailsResponse();

        $this->getDomainAndHttpHost();
        Reception::$requestMethod =       $this->routeDto->requestMethod;
        Reception::$isJson =              $this->routeDto->isJson;

        Reception::$flashSession =        $this->getFlashSession();
        Reception::$inputData =           $this->parseRequestBody();
    }

    public static function getDomainAndHttpHost(): string
    {
        if (isset(Reception::$domain)) {
            return Reception::$domain;
        }

        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        Reception::$domain = $protocol . '://' . ($_SERVER['HTTP_HOST'] ?? '') . URL_ROOT;

        return Reception::$domain;
    }

    /**
     * Get the flash session data if it exists and unset it from the session.
     *
     * @return array The flash session data
     */
    private function getFlashSession(): array
    {
        if (isset($_SESSION[FLASH_SESSION_KEY_NAME])) {
            $session = $_SESSION[FLASH_SESSION_KEY_NAME];
            unset($_SESSION[FLASH_SESSION_KEY_NAME]);
        } else {
            $session = [];
        }

        return $session;
    }

    /**
     * Parses the request body and returns the input data.
     *
     * @return array The input data passed with the incoming request.
     */
    private function parseRequestBody(): array
    {
        if (Reception::$requestMethod === 'GET') {
            return array_merge($_GET, $this->routeDto->paramArray);
        }

        if (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
            return array_merge($_GET, $this->parseJson(), $this->routeDto->paramArray);
        }

        return array_merge($_GET, $_POST, $_FILES, $this->routeDto->paramArray);
    }

    private function parseJson(): array
    {
        $requestBody = file_get_contents('php://input');
        if (!is_string($requestBody)) {
            return [];
        }

        $jsonArray = json_decode($requestBody, true);
        if (!is_array($jsonArray)) {
            return [];
        }

        return $jsonArray;
    }

    /**
     * Validate the incoming request using the built-in validators and the route callback validator, if available.
     * Store the validated input data in the static Reception::$inputData property.
     * 
     * @param array $inputArray
     * 
     * @return array Validated array
     * 
     * @throws InvalidArgumentException
     * @throws ValidationException
     * @throws NotFoundException
     */
    public function callRequestValidator()
    {
        if (!empty($_FILES)) {
            $this->checkUploadError();
        }

        $builtinValidators = $this->routeDto->getValidater();
        if ($builtinValidators !== false) {
            $validatedArray = $this->callBuiltinValidator($builtinValidators);
            Reception::$inputData = array_merge(Reception::$inputData, $validatedArray);
        }
    }

    private function checkUploadError()
    {
        foreach ($_FILES as $key => $file) {
            try {
                $this->isUploadError($file);
            } catch (UploadException $e) {
                $errors[] = [
                    'key' => $key,
                    'code' => $e->getCode(),
                    'message' => $e->getMessage()
                ];
            }
        }

        if (!empty($errors)) {
            $this->errorResponse($errors, UploadException::class);
        }
    }

    private function isUploadError(array $file)
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new UploadException('An error occurred while uploading the file.', 999);
        }
    }

    private function callBuiltinValidator(array $validators): array
    {
        $validatedArray = [];
        $errors = [];

        foreach ($validators as $key => $validator) {
            $data = Reception::$inputData;
            $currentLevel = &$validatedArray;

            foreach (explode('.', $key) as $property) {
                $data = &$data[$property] ?? null;
                $currentLevel[$property] = null;
                $currentLevel = &$currentLevel[$property];
            }

            try {
                $validatedValue = $validator($data);
            } catch (ValidationException $e) {
                $errors[] = [
                    'key' => $key,
                    'code' => $e->getCode(),
                    'message' => $e->getMessage()
                ];

                $validatedValue = null;
            }

            $currentLevel = $validatedValue;
        }

        if (!empty($errors)) {
            $this->errorResponse($errors);
        }

        return $validatedArray;
    }
}
