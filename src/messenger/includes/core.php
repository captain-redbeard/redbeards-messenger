<?php
/**
 * 
 * Details:
 * Small collection of helpful functions.
 * 
 * Modified: 03-Dec-2016
 * Made Date: 25-Nov-2016
 * Author: Hosvir
 * 
 * */

/**
 * 
 * Generate a random string.
 * 
 * */
function generateRandomString($length)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[mt_rand(0, strlen($characters) - 1)];
    }
    
    return $randomString;
}

/**
 * 
 * Check if one string contains the other.
 * 
 * */
function contains($contains, $container)
{
    return strpos(strtolower($container), strtolower($contains)) !== false;
}

/**
 * 
 * Convert the time to the specified timezone.
 * 
 * */
function convertTime($time_convert, $short = false)
{
    $userTime = new DateTime($time_convert, new DateTimeZone(TIMEZONE));
    $userTime->setTimezone(new DateTimeZone($_SESSION[USESSION]->timezone));
    if (!$short) { 
        return $userTime->format('Y-m-d h:i:s A');
    } else {
        return date('d/m') != $userTime->format('d/m') ? $userTime->format('d/m h:i A') : $userTime->format('h:i A');
    }
}

/**
 * 
 * Clean the passed input to remove any unwanted characters.
 * 
 * */
function cleanInput($input, $level = 2)
{
    switch ($level) {
        case 0:
            $clean = str_replace("\n", "---newline---", $input);
            $clean = strip_tags($clean, '<ul><li><b><u>');
            $clean = preg_replace('/[^a-zA-Z0-9 \-_\/ @.,!\'?#$%^&*()+={}\[\]":;<>`~]/i',' ', $clean);
            break;
        case 1:
            $clean = strip_tags($input);
            $clean = preg_replace('/[^a-zA-Z0-9 \-_\/ @.]/i',' ', $clean);
            break;
        default:
            $clean = strip_tags($input);
            $clean = preg_replace('/[^a-zA-Z0-9 \-]/i',' ', $clean);
            break;
    }

    return $clean;
}


/**
 * 
 * Convert a date to a nice displayable time.
 * 
 * */
function niceTime($date)
{
    if (empty($date)) return "No date provided";
    
    $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
    $lengths = array("60","60","24","7","4.35","12","10");
    $now = time();
    $unix_date = strtotime($date);
    
    if (empty($unix_date)) return "Bad date";

    if ($now > $unix_date) {   
        $difference = $now - $unix_date;
        $tense = "ago";
    } else {
        $difference = $unix_date - $now;
        $tense = "";
    }
    
    for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
        $difference /= $lengths[$j];
    }
    
    $difference = round($difference);
    
    if ($difference != 1) $periods[$j].= "s";
    
    return "$difference $periods[$j] {$tense}";
}

/**
 * 
 * Get the current directory as a URL.
 * 
 * */
function getDirectoryAsURL()
{
    $url = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    $url .= $_SERVER['SERVER_NAME'] . str_replace($_SERVER['DOCUMENT_ROOT'], "", dirname(__DIR__));
    return $url;
}

/**
 * 
 * Get the current URL.
 * 
 * */
function getURL()
{
    $url = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    $url .= $_SERVER['SERVER_NAME'] . str_replace($_SERVER['DOCUMENT_ROOT'], "", $_SERVER['PHP_SELF']);
    return $url;
}
?>
