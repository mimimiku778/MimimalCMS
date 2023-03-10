<?php

class IndexPageController extends AbstractPageController
{
    public function index()
    {
        View::render('test_header', ['title' => 'MimimalCMS']);

        View::render('test_content', [
            'headerTitle' => 'Hello, world.',
            'text' => 'This is a test.'
        ]);
        
        View::render('test_footer', ['footerText' => 'Good bye.']);

        View::display();
    }
}
