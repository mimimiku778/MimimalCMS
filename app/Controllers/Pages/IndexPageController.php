<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Views\MetaTags;

class IndexPageController
{
    public function index(MetaTags $_metaTags)
    {   
        $text = "Hello Wolrd";
        $link = "https://github.com/mimimiku778/MimimalCMS";

        return view('test_header', compact('_metaTags'))
            ->make('test_content', compact('text'))
            ->make('test_footer',  compact('link'));
    }
}
