<?php

abstract class AbstractApiController
{
    public function __construct()
    {
        $jsonData = file_get_contents('php://input');
        if (!is_string($jsonData) || empty(trim($jsonData))) {
            return;
        }

        $json = json_decode($jsonData, true);
        if (is_array($json)) {
            $_POST = $json;
            return;
        }
    }

    abstract public function index();

    /**
     * Returns HTTP status code and response in JSON format and exits.
     *
     * @param array $data The array to be returned as response.
     * @param int $response_code [optional] HTTP status code
     */
    public function response(array $data, int $response_code = 200)
    {
        http_response_code($response_code);
        header("Content-Type: application/json; charset=utf-8");
        ob_start('ob_gzhandler');
        exit(json_encode($data));
    }
}
