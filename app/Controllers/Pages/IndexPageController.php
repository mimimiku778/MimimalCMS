<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Config\ConfigJson;

class IndexPageController
{
    public function index(ConfigJson $config)
    {
        $title = $config->siteTitle;
        $text = 'Hello Wolrd';
        $link = 'https://github.com/mimimiku778/MimimalCMS-v0.1';

        return view('test_header', compact('title'))
            ->make('test_content', compact('text'))
            ->make('test_footer',  compact('link'));
    }
}
