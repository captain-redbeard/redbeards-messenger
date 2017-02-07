<?php
/**
 * @author captain-redbeard
 * @since 05/02/17
 */
namespace Redbeard\Core;

class Config
{
    public static $configs = [];
    
    public static function init()
    {
        $config = require_once '../app/config.php';
        self::load($config);
    }
    
    public static function load($config)
    {
        foreach ($config as $key => $value) {
            self::set($key, $value);
        }
    }
    
    public static function get($key)
    {
        $split = explode('.', $key);
        
        switch (count($split)) {
            case 1:
                return self::$configs[$split[0]];
                break;
            case 2:
                return self::$configs[$split[0]][$split[1]];
                break;
            case 3:
                return self::$configs[$split[0]][$split[1]][$split[2]];
                break;
            default:
                return self::$configs[$split[0]][$split[1]][$split[2]][$split[3]];
                break;
        }
    }
    
    public static function set($key, $value)
    {
        $split = explode('.', $key);
        
        switch (count($split)) {
            case 1:
                self::$configs[$split[0]] = $value;
                break;
            case 2:
                self::$configs[$split[0]][$split[1]] = $value;
                break;
            case 3:
                self::$configs[$split[0]][$split[1]][$split[2]] = $value;
                break;
            default:
                self::$configs[$split[0]][$split[1]][$split[2]][$split[3]] = $value;
                break;
        }
    }
}
