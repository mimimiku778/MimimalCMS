<?php

abstract class AbstractApiController
{
    abstract public function index();

    /**
     * Returns HTTP status code and response in JSON format and exits.
     *
     * @param int $response_code HTTP status code
     * @param mixed $value The value to be returned as a response. Default is an empty string.
     */
    public function response(int $response_code, mixed $value = '')
    {
        http_response_code($response_code);
        header("Content-Type: application/json; charset=utf-8");
        ob_start('ob_gzhandler');
        exit(json_encode($value));
    }
}
