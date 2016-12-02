<?php
/**
 *
 * Details:
 * This class is to provide static methods to easily use MySQLI 
 * prepared statements.
 * 
 * Modified: 09-Sep-2015
 * Made Date: 09-Sep-2015
 * Author: Hosvir
 * 
 * */
class QB {
	
	/**
	 * Prepare the query.
	 * 
	 * @usage: QB::prepare("SELECT email, first_name FROM users;",
	 * 						$mysqli);
	 * 
	 * @retunrs: Prepared statement
	 * */
	public static function prepare($query,$mysqli){
		//Prepare the query
		$stmt = $mysqli->prepare($query);
		if(!$stmt) throw new Exception("Error in preparing statement: " . $mysqli->error);
		
		//Return the prepared statement
		return $stmt;
	}
	
	/**
	 * Select query with array parameters.
	 * 
	 * @usage: $results = QB::select("SELECT email, first_name FROM users WHERE user_id = ?;",
	 * 						array(1),
	 * 						$mysqli);
	 * 
	 * echo $results[0]["username"];
	 * 
	 * @returns: Array of results, [index][field_name]
	 * */
	public static function select($query,$params,$mysqli){
		//Prepare the query
		$stmt = QB::prepare($query,$mysqli);
		
		//Bind parameters
		if(count($params) > 0 && $params[0] != null)
			call_user_func_array(array($stmt, "bind_param"), QB::get_types($params));
		
		//Execute the query
		$stmt->execute();
		
		//Get metadata
		$meta = $stmt->result_metadata();
		
		//Create empty arrays
		$fields = $results = array();

		//Populate fields
		while($field = $meta->fetch_field()) { 
			$var = $field->name; 
			$$var = null; 
			$fields[$var] = &$$var; 
		}
		
		//Get field count
		$fieldCount = count($fields);

		//Bind Results                                     
		call_user_func_array(array($stmt,'bind_result'),$fields);

		//Fetch Results
        $i = 0;
        while($stmt->fetch()) {
            $results[$i] = array();
            foreach($fields as $k => $v) $results[$i][$k] = $v;
            $i++;
        }

		//Return results
		return $results;
	}
		
	/**
	 * Update query with array parameters.
	 * 
	 * @usage: QB::update("UPDATE users SET name = ? WHERE user_id = ?",
	 * 						array("Example",1),
	 * 						$mysqli);
	 * 
	 * @retunrs: Update count
	 * */
	public static function update($query,$params,$mysqli){
		//Prepare the query
		$stmt = QB::prepare($query,$mysqli);
		
		//Bind parameters
		if(count($params) > 0) call_user_func_array(array($stmt, "bind_param"), QB::get_types($params));
		
		//Execute the query and return the update count
		return $stmt->execute();
	}
	
	/**
	 * Insert query with array parameters.
	 * 
	 * @usage: QB::insert("INSERT INTO users(user_id) VALUES (?);",
	 * 						array(1),
	 * 						$mysqli);
	 * 
	 * @returns: Returns the insert ID
	 * */
	public static function insert($query,$params,$mysqli){
		//Prepare the query
		$stmt = QB::prepare($query,$mysqli);
		
		//Bind parameters
		if(count($params) > 0) call_user_func_array(array($stmt, "bind_param"), QB::get_types($params));
		
		//Execute the query
		if(!$stmt->execute()) throw new Exception("Error executing statement: " . $mysqli->error);
		
		return $mysqli->insert_id;
	}
	
	/**
	 * Get the type of the parameter.
	 * 
	 * @returns: i,d,s or b.
	 * */
	public static function get_type($param){
		switch(gettype($param)){
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
	 * @returns: Array with types in [0]
	 * */
	public static function get_types($params){
		$types = "";
		
		//Append each type
		foreach($params as $param)      
			$types .= QB::get_type($param);
		
		//Append types
		array_unshift($params, $types);
		
		//Return the array reference
		return QB::ref_values($params);
	}
	
	/**
	 * Reference array values.
	 * */
	private static function ref_values($array) {
		$refs = array();
		foreach($array as $key => $value) $refs[$key] = &$array[$key]; 
		return $refs; 
	}
	
}
?>
