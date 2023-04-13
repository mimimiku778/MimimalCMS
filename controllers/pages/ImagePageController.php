<?php

class ImagePageController
{
    public function index()
    {
        $title = 'Image Uploader';
        $link = 'https://github.com/mimimiku778';

        return view('test_header', compact('title'))
            ->make('test_image_uploader')
            ->make('test_footer',  compact('link'));
    }
}
