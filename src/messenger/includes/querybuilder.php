<?php
/**
 *
 * Details:
 * This class is to provide static methods to easily use MySQLI 
 * prepared statements.
 * 
 * Modified: 03-Dec-2015
 * Made Date: 09-Sep-2015
 * Author: Hosvir
 * 
 * */
include(dirname(__FILE__) . "/database.php");

class QueryBuilder
{

    /**
     * Prepare the query.
     * 
     * @usage:
     * QueryBuilder::prepare(
     *  "SELECT email, first_name FROM users;",
     *  $mysqli
     * );
     *
     * @param: $query   - SQL Query
     * @param: $mysqli  - MySQLi connection
     * 
     * @retunrs: Prepared statement
     * */
    private static function prepare($query, $mysqli)
    {
        //Prepare the query
        $stmt = $mysqli->prepare($query);
        if (!$stmt) throw new Exception("Error in preparing statement: " . $mysqli->error);

        //Return the prepared statement
        return $stmt;
    }

    /**
     * Select query with array parameters.
     * 
     * @usage:
     * $results = QueryBuilder::select(
     *  "SELECT email, first_name FROM users WHERE user_id = ?;",
     *  array(1),
     *  $mysqli
     * );
     * 
     * echo $results[0]["username"];
     *
     * @param: $query       - SQL Query
     * @param: $parameters  - Query parameters to bind
     * @param: $mysqli      - MySQLi connection
     * 
     * @returns: Array of results, [index][field_name]
     * */
    public static function select($query, $parameters)
    {
        $mysqli = Database::connect();
        
        //Prepare the query
        $stmt = QueryBuilder::prepare($query, $mysqli);

        //Bind parameters
        if (count($parameters) > 0 && $parameters[0] != null) {
            call_user_func_array(array($stmt, "bind_param"), QueryBuilder::get_types($parameters));
        }

        //Execute the query
        $stmt->execute();

        //Get metadata
        $meta = $stmt->result_metadata();

        //Create empty arrays
        $fields = $results = array();

        //Populate fields
        while ($field = $meta->fetch_field()) { 
            $var = $field->name; 
            $$var = null; 
            $fields[$var] = &$$var; 
        }

        //Get field count
        $fieldCount = count($fields);

        //Bind Results                                     
        call_user_func_array(array($stmt, 'bind_result'), $fields);

        //Fetch Results
        $i = 0;
        while ($stmt->fetch()) {
            $results[$i] = array();
            foreach ($fields as $k => $v) $results[$i][$k] = $v;
            $i++;
        }

        //Return results
        return $results;
    }

    /**
     * Update query with array parameters.
     * 
     * @usage:
     * QueryBuilder::update(
     *  "UPDATE users SET name = ? WHERE user_id = ?",
     *  array("Example",1),
     *  $mysqli
     * );
     *
     * @param: $query       - SQL Query
     * @param: $parameters  - Query parameters to bind
     * @param: $mysqli      - MySQLi connection
     * 
     * @retunrs: Update count
     * */
    public static function update($query, $parameters)
    {
        $mysqli = Database::connect();
        
        //Prepare the query
        $stmt = QueryBuilder::prepare($query, $mysqli);

        //Bind parameters
        if (count($parameters) > 0) call_user_func_array(array($stmt, "bind_param"), QueryBuilder::get_types($parameters));

        //Execute the query and return the update count
        return $stmt->execute();
    }

    /**
     * Insert query with array parameters.
     * 
     * @usage:
     * QueryBuilder::insert(
     *  "INSERT INTO users(user_id) VALUES (?);",
     *  array(1),
     *  $mysqli
     * );
     *
     * @param: $query       - SQL Query
     * @param: $parameters  - Query parameters to bind
     * @param: $mysqli      - MySQLi connection
     * 
     * @returns: Returns the insert ID
     * */
    public static function insert($query, $parameters)
    {
        $mysqli = Database::connect();
        
        //Prepare the query
        $stmt = QueryBuilder::prepare($query,$mysqli);

        //Bind parameters
        if (count($parameters) > 0) call_user_func_array(array($stmt, "bind_param"), QueryBuilder::get_types($parameters));

        //Execute the query
        if (!$stmt->execute()) throw new Exception("Error executing statement: " . $mysqli->error);

        return $mysqli->insert_id;
    }

    /**
     * Get the type of the parameter.
     *
     * @param: $parameter   - Query parameter to get type of
     * 
     * @returns: i,d,s or b.
     * */
    public static function get_type($parameter)
    {
        switch (gettype($parameter)) {
            case "integer":
                return "i";
                break;
            case "double":
                return "d";
                break;
            case "string":
                return "s";
                break;
            default:
                return "b";
                break;
        }
    }

    /**
     * Get all types of the passed param array.
     *
     * @param: $parameters  - Query parameters to bind
     * 
     * @returns: Array with types in [0]
     * */
    public static function get_types($parameters)
    {
        $types = "";

        //Append each type
        foreach ($parameters as $parameter) {
            $types .= QueryBuilder::get_type($parameter);
        }

        //Append types
        array_unshift($parameters, $types);

        //Return the array reference
        return QueryBuilder::ref_values($parameters);
    }

    /**
     * Reference array values.
     * */
    private static function ref_values($array)
    {
        $refs = array();
        foreach ($array as $key => $value) $refs[$key] = &$array[$key]; 
        return $refs; 
    }
}
