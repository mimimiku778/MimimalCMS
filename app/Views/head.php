<head prefix="og: http://ogp.me/ns#">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo $_meta ?>
    <?php foreach ($_css ?? [] as $css) : ?>
        <link rel="stylesheet" href="<?php echo fileUrl('assets/' . $css) ?>">
    <?php endforeach ?>
    <link rel="apple-touch-icon" type="image/png" href="<?php echo fileUrl('assets/apple-touch-icon-180x180.png') ?>">
    <link rel="icon" type="image/png" href="<?php echo fileUrl('assets/icon-192x192.png') ?>">
    <link rel="icon" type="image/png" href="<?php echo fileUrl('favicon.png') ?>">
</head>