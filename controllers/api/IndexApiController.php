<?php

class IndexApiController extends AbstractApiController
{
    public function index()
    {
        $this->response($_POST);
    }
}
