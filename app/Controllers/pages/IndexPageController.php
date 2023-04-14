<?php

class IndexPageController
{
    public function index()
    {
        $title = 'MimimalCMS 0.1';
        $text = 'Hello Wolrd';
        $link = 'https://github.com/mimimiku778/MimimalCMS-v0.1';

        return view('test_header', compact('title'))
            ->make('test_content', compact('text'))
            ->make('test_footer',  compact('link'));
    }
}
