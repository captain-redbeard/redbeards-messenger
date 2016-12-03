<?php
/**
 * 
 * Details:
 * PHP Messenger.
 * 
 * Modified: 03-Dec-2016
 * Made Date: 25-Nov-2016
 * Author: Hosvir
 * 
 * */
function minify_output($buffer)
{
    $replace = array(
        '/\>[^\S ]+/s'   => '>',
        '/[^\S ]+\</s'   => '<',
        '/([\t ])+/s'  => ' ',
        '/^([\t ])+/m' => '',
        '/([\t ])+$/m' => '',
        '~//[a-zA-Z0-9 ]+$~m' => '',
        '/[\r\n]+([\t ]?[\r\n]+)+/s'  => "\n",
        '/\>[\r\n\t ]+\</s'    => '><',
        '/}[\r\n\t ]+/s'  => '}',
        '/}[\r\n\t ]+,[\r\n\t ]+/s'  => '},',
        '/\)[\r\n\t ]?{[\r\n\t ]+/s'  => '){',
        '/,[\r\n\t ]?{[\r\n\t ]+/s'  => ',{',
        '/\),[\r\n\t ]+/s'  => '),',
        '~([\r\n\t ])?([a-zA-Z0-9]+)="([a-zA-Z0-9_/\\-]+)"([\r\n\t ])?~s' => '$1$2=$3$4' 
    );
    return preg_replace(array_keys($replace), array_values($replace), $buffer);
}

ob_start("minify_output");

//Get variables
if (isset($_GET['page'])) $page = $_GET['page'];
?>
<!DOCTYPE html>
<html lang="en-AU"> 
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=yes">
        <link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">

        <style>
            *,*:before,*:after { -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box; }
            html,body { margin: 0; padding: 0; line-height: 1; width: 100%; position: relative; font-family: 'Ubuntu', sans-serif; }
            .co { width: 100%; height: 100vh; margin: 0 auto; }
        </style>

        <base href="https://yourdomain.com">
        <?php if (isset($page) && $page == "conversations") { ?>

        <link rel="stylesheet" href="./css/m.css">
        <?php } else { ?>

        <link rel="stylesheet" href="./css/styles.css">
        <?php } ?>
        <title>PHP Messenger</title>
    </head>

    <body>
        <div class="co">
            <?php include("../messenger/messenger.php"); ?>
        </div>
        <?php if (isset($page) && $page == "conversations") { ?>

        <script type="text/JavaScript" src="./js/m.min.js"></script>
        <?php } ?>

    </body>
</html>
<?php ob_end_flush(); ?>
