<!DOCTYPE html>
<html lang="en-AU"> 
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=yes">
        <link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">

        <base href="<?php echo BASE_HREF; ?>">
        <?php if ($data['page'] == "conversations") { ?>

        <link rel="stylesheet" href="./css/m.css">
        <?php } else { ?>

        <link rel="stylesheet" href="./css/styles.css">
        <?php } ?>
        
        <title><?php echo $data['page_title']; ?></title>
    </head>

    <body>
        <div class="co">
