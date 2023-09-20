<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo fileUrl('assets/mvp.css') ?>">
    <link rel="icon" type="image/png" href="<?php echo fileUrl('favicon.png') ?>">
    <title><?php echo $title ?? '' ?></title>
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
        <h1><?php echo $title ?? '' ?></h1>
    </header>