<?php

class IndexPageController extends AbstractPageController
{
    public function index()
    {
        View::render(
            'test_template',
            [
                'title' => 'MimimalCMS',
                'headerTitle' => 'Hello, world'
            ]
        );

        View::display();
    }
}
