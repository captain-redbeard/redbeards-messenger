<?php
/**
 *
 * Details:
 * PHP Messenger.
 *
 * Modified: 11-Dec-2016
 * Made Date: 05-Nov-2016
 * Author: Hosvir
 *
 */
namespace Messenger\Core;

use \PDO;

class Database
{
    public static $connection = null;
    
    public static function connect($rdbms = 'mysql')
    {
        if (self::$connection === null) {
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ];
            
            self::$connection = new PDO(
                $rdbms . ':' .
                'host=' . DB_HOSTNAME . ';' .
                'dbname=' . DB_DATABASE . ';' .
                'chatset=' . DB_CHARSET . ';',
                DB_USERNAME,
                DB_PASSWORD,
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
        return self::connect()->prepare($query);
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
