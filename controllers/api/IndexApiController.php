<?php

class IndexApiController extends AbstractApiController
{
    public function index()
    {
        $this->response(200, 'Hello World');
    }
}
