<!DOCTYPE html>
<!-- TODO: Local -->
<html lang="ja">

<head prefix="og: http://ogp.me/ns#">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo $_metaTags ?? '' ?>
    <link rel="icon" type="image/png" href="<?php echo fileUrl(\App\Config\AppConfig::SITE_ICON_FILE_PATH) ?>">
    <link rel="stylesheet" href="<?php echo fileUrl('assets/mvp.css') ?>">
</head>

<body>

    <header>
        <nav>
            <a href="<?php echo url() ?>">HOME</a>
            <ul>
                <li>
                    <a href="/image">Image Uploader</a>
                </li>
            </ul>
        </nav>
        <h1><?php echo (isset($_metaTags) ? $_metaTags->getCurrentTitle() : '') ?></h1>
    </header>