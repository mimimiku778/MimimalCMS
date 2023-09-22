<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Config\ConfigJson;
use App\Views\MetaTags;

class IndexPageController
{
    public function index(ConfigJson $config, MetaTags $_metaTags)
    {   
        $text = $config->topPageText;
        $link = $config->footerLink;

        return view('test_header', compact('_metaTags'))
            ->make('test_content', compact('text'))
            ->make('test_footer',  compact('link'));
    }
}
