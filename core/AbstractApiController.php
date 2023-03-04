<?php

abstract class AbstractApiController
{
    abstract public function index();

    /**
     *  HTTPステータスコードと、JSON形式でレスポンスを返して終了する
     * 
     *  @param int $response_code HTTPステータスコード
     *  @param mixed $value レスポンスとして返す値。デフォルトは空文字列
     */
    public function response(int $response_code, mixed $value = '')
    {
        http_response_code($response_code);
        header("Content-Type: application/json; charset=utf-8");
        ob_start('ob_gzhandler');
        exit(json_encode($value));
    }
}
