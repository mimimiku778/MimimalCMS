<?php

class IndexPageController extends AbstractPageController
{
    public function index()
    {
        echo '<h1>Hello World</h1>';
        require_once __DIR__ . '/../../core/SqlConnect.php';

        $sql = new SqlConnect();
        
        $test = $sql->fetchAll('SELECT * FROM test');
        var_dump($test);
    }
}
