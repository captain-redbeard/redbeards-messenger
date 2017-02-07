<?php
/**
 * @author captain-redbeard
 * @since 20/01/17
 */
namespace Redbeard\Core;

use \PDO;
use Redbeard\Core\Config;

class Database
{
    private static $connection = null;
    private static $config = [];
    
    public static function init($config)
    {
        self::$config = $config;
    }
    
    public static function connect($config)
    {
        if (self::$connection === null) {
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ];
            
            self::$connection = new PDO(
                $config['rdbms'] . ':' .
                'host=' . $config['hostname'] . ';' .
                'dbname=' . $config['database'] . ';' .
                'chatset=' . $config['charset'] . ';',
                $config['username'],
                $config['password'],
                $options
            );
        }
        
        return self::$connection;
    }
    
    public static function close()
    {
        self::$connection = null;
    }
    
    private static function prepare($query)
    {
        return self::connect(self::$config)->prepare($query);
    }
    
    public static function select($query, $parameters)
    {
        $stmt = self::prepare($query);
        $stmt->execute($parameters);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function update($query, $parameters)
    {
        $stmt = self::prepare($query);
        return $stmt->execute($parameters);
    }
    
    public static function insert($query, $parameters)
    {
        $stmt = self::prepare($query);
        $stmt->execute($parameters);
        return self::$connection->lastInsertId();
    }
    
    private static function getType($parameter)
    {
        switch ($parameter) {
            case is_int($parameter):
                return PDO::PARAM_INT;
            case is_bool($parameter):
                return PDO::PARAM_BOOL;
            case is_null($parameter):
                return PDO::PARAM_NULL;
            default:
                return PDO::PARAM_STR;
        }
    }
    
    private static function getTypes($parameters)
    {
        $types = [];
        
        for ($i = 0; $i < count($parameters); $i++) {
            $types[$i] = self::getType($parameters[$i]);
        }
        
        array_unshift($parameters, $types);
        
        return $types;
    }
    
    private static function refValues($array)
    {
        $refs = [];
        
        foreach ($array as $key => $value) {
            $refs[$key] = &$array[$key];
        }
        
        return $refs;
    }
}
