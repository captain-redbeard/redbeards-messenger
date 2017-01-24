<!DOCTYPE html>
<html lang="en-AU">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=yes">
        <meta name="theme-color" content="#4aa3df">
		<meta name="msapplication-navbutton-color" content="#4aa3df">
		<meta name="apple-mobile-web-app-status-bar-style" content="#4aa3df">
        
        <base href="<?=BASE_HREF;?>/">
        <link rel="shortcut icon" href="<?=BASE_HREF;?>/favicon.ico" type="image/x-icon">
        <?php if ($data['page'] === "conversations") { ?>

        <link rel="stylesheet" href="<?=BASE_HREF;?>/css/m.css">
        <?php } else { ?>

        <link rel="stylesheet" href="<?=BASE_HREF;?>/css/styles.css">
        <?php } ?>
        
        <title><?=$data['page_title'];?></title>
    </head>

    <body>
        <div class="co">
