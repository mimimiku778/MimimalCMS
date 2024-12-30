<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Views\MetaTags;

class ImagePageController
{
    public function index(MetaTags $_metaTags)
    {
        $_metaTags->setCurrentTitle('Image Uploader');
        $link = "https://github.com/mimimiku778/MimimalCMS";

        return view('test_header', compact('_metaTags'))
            ->make('test_image_uploader')
            ->make('test_footer',  compact('link'));
    }
}
