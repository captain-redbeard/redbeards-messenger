<?php
/**
 *
 * Details:
 * PHP Messenger.
 *
 * Modified: 08-Dec-2016
 * Made Date: 05-Nov-2016
 * Author: Hosvir
 *
 */
namespace Messenger\Core;

use \DateTime;
use \DateTimeZone;
use \Tidy;

class Functions
{
    
    public static function generateRandomString($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[mt_rand(0, strlen($characters) - 1)];
        }
        
        return $randomString;
    }
    
    public static function contains($contains, $container)
    {
        return strpos(strtolower($container), strtolower($contains)) !== false;
    }
    
    public static function convertTime($time_convert, $short = false)
    {
        $userTime = new DateTime($time_convert, new DateTimeZone(TIMEZONE));
        $userTime->setTimezone(new DateTimeZone($_SESSION[USESSION]->timezone));
        if (!$short) {
            return $userTime->format('Y-m-d h:i:s A');
        } else {
            return date('d/m') != $userTime->format('d/m') ?
                $userTime->format('d/m h:i A') :
                $userTime->format('h:i A');
        }
    }
    
    public static function cleanInput($input, $level = 2)
    {
        switch ($level) {
            case 0:
                //NO FILTERING, MUST FILTER BEFORE DISPLAYING
                $clean = $input;
                break;
            case 1:
                $clean = strip_tags($input);
                $clean = preg_replace('/[^a-zA-Z0-9 \-_\/ @.]/i', ' ', $clean);
                break;
            default:
                $clean = strip_tags($input);
                $clean = preg_replace('/[^a-zA-Z0-9 \-]/i', ' ', $clean);
                break;
        }
        
        return $clean;
    }
    
    public static function niceTime($date)
    {
        if (empty($date)) {
            return "No date provided";
        }
        
        $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
        $lengths = array("60","60","24","7","4.35","12","10");
        $now = time();
        $unix_date = strtotime($date);
        
        if (empty($unix_date)) {
            return "Bad date";
        }
        
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
        
        if ($difference != 1) {
            $periods[$j].= "s";
        }
        
        return "$difference $periods[$j] {$tense}";
    }
    
    public static function getDirectoryAsUrl()
    {
        $url = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $url .= $_SERVER['SERVER_NAME'] . str_replace($_SERVER['DOCUMENT_ROOT'], "", dirname(__DIR__));
        return $url;
    }
    
    public static function getUrl()
    {
        $url = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $url .= $_SERVER['SERVER_NAME'] . str_replace($_SERVER['DOCUMENT_ROOT'], "", $_SERVER['PHP_SELF']);
        return $url;
    }
    
    public static function allowTags($message)
    {
        $allowed = [
            '/\\n/',
            '/&lt;br&gt;/',
            '/&lt;b&gt;/',
            '/&lt;\/b&gt;/',
            '/&lt;ul&gt;/',
            '/&lt;\/ul&gt;/',
            '/&lt;li&gt;/',
            '/&lt;\/li&gt;/',
            '/&lt;strong&gt;/',
            '/&lt;\/strong&gt;/'
        ];
        
        $replace = [
            '<br>',
            '<br>',
            '<b>',
            '</b>',
            '<ul>',
            '</ul>',
            '<li>',
            '</li>',
            '<strong>',
            '</strong>'
        ];
        
        $tidy = new Tidy();
        
        return $tidy->repairString(
            preg_replace(
                $allowed,
                $replace,
                $message
            ),
            [
                'show-body-only' => true,
                'indent' => true,
                'indent-spaces' => 4
            ]
        );
    }
}
