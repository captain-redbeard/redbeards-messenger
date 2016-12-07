<?php
/**
 * 
 * Details:
 * PHP Messenger.
 * 
 * Modified: 07-Dec-2016
 * Made Date: 05-Nov-2016
 * Author: Hosvir
 * 
 * */
namespace Messenger\Core;

use \PDO;

class Database 
{
    public static $connection = null;

    /**
     *
     * Connect to the specified RDBMS.
     *
     * @param: All other parameters are set as global.
     *
     * @returns: Connection instance.
     * */
    public static function connect($rdbms = 'mysql')
    {
        if (self::$connection == null ) {
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ];
            
            self::$connection = new PDO(
                $rdbms . ':' .
                'host=' . DB_HOSTNAME . ';' .
                'dbname=' . DB_DATABASE . ';' .
                'chatset=utf8mb4;',
                DB_USERNAME,
                DB_PASSWORD,
                $options
            );
        }
        
        return self::$connection;
    }

    /**
     * 
     * Close the current connection.
     *
     * */
    public static function close()
    {
        self::$connection = null;
    }

    /**
     * Prepare the query.
     * 
     * @usage:
     * Database::prepare(
     *  "SELECT email, first_name FROM users;",
     *  []
     * );
     *
     * @param: $query   - SQL Query
     * 
     * @returns: Prepared statement
     * */
    private static function prepare($query)
    {
        return self::connect()->prepare($query);
    }

    /**
     * Select query with array parameters.
     * 
     * @usage:
     * $results = Database::select(
     *  "SELECT email, first_name FROM users WHERE user_id = ?;",
     *  [1]
     * );
     * 
     * echo $results[0]["username"];
     *
     * @param: $query       - SQL Query
     * @param: $parameters  - Query parameters to bind
     * 
     * @returns: Array of results, [index][field_name]
     * */
    public static function select($query, $parameters)
    {
        //Prepare the query
        $stmt = self::prepare($query);

        //Execute the query
        $stmt->execute($parameters);

        //Return array
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update query with array parameters.
     * 
     * @usage:
     * QueryBuilder::update(
     *  "UPDATE users SET name = ? WHERE user_id = ?",
     *  ["Example",1]
     * );
     *
     * @param: $query       - SQL Query
     * @param: $parameters  - Query parameters to bind
     * 
     * @retunrs: Update count
     * */
    public static function update($query, $parameters)
    {        
        //Prepare the query
        $stmt = self::prepare($query);

        //Execute the query and return the update count
        return $stmt->execute($parameters);
    }

    /**
     * Insert query with array parameters.
     * 
     * @usage:
     * QueryBuilder::insert(
     *  "INSERT INTO users(user_id) VALUES (?);",
     *  [1]
     * );
     *
     * @param: $query       - SQL Query
     * @param: $parameters  - Query parameters to bind
     * 
     * @returns: Returns the insert ID
     * */
    public static function insert($query, $parameters)
    {        
        //Prepare the query
        $stmt = self::prepare($query);
        
        //Execute the query
        $stmt->execute($parameters);

        return self::$connection->lastInsertId();
    }

    /**
     * Get the type of the parameter.
     *
     * @param: $parameter   - Query parameter to get type of
     * 
     * @returns: PDO Constant
     * */
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

    /**
     * Get all types of the passed param array.
     *
     * @param: $parameters  - Query parameters to bind
     * 
     * @returns: Array with types in [0]
     * */
    private static function getTypes($parameters)
    {
        $types = [];

        //Append each type
        for($i = 0; $i < count($parameters); $i++) {
            $types[$i] = self::getType($parameters[$i]);
        }

        print_r($types);

        //Append types
        array_unshift($parameters, $types);

        //Return the array reference
        return $types;
    }

    /**
     * 
     * Reference array values.
     * 
     * */
    private static function refValues($array)
    {
        $refs = [];
        
        foreach ($array as $key => $value) {
            $refs[$key] = &$array[$key];
        }

        print_r($refs);
        return $refs; 
    }
}
