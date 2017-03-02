<?php
class SQL {

	protected static $debug = false;
	
	protected static $mysqli = NULL;
	protected static function connect() 
	{
		if(self::$mysqli !== NULL)
			return;	
		include('passwords.php');
		
		self::$mysqli = new mysqli($dblocation, $dbuser, $dbpasswd, $dbname); 
		if (mysqli_connect_errno()) { 
			printf("Подключение невозможно: %s\n", mysqli_connect_error()); 
			exit(); 
		} 
		mysqli_set_charset(self::$mysqli, 'utf8');
		setlocale(LC_ALL, 'ru_RU.UTF-8');
	}
	
	protected static function StringFromArray(array $array, $divisor) {
		if(!is_string($divisor)) {
			return NULL;
		}
		if(count($array) == 0) {
			return '';
		}
		$result = $array[0];
		for($i = 1; $i < count($array); $i++) {
			$result .= ' ' . $divisor . ' ' . $array[$i];
		}
		return $result;
	}
	
		protected static function StringFromAssoc(array $values){
		if(!self::is_assoc($values)) 
			return NULL;
		$result = "";
		foreach ($values as $key => $value){
			$result = $result . $key . '=' . $value . ',';
		}
		$result[strlen($result) - 1] = " ";
		return $result;
	}
	
	protected static function is_assoc(array $arr)
	{
		if (array() === $arr) return false;
		return array_keys($arr) !== range(0, count($arr) - 1);
	}
	
	protected static function MakeQuery_INSERT_set($table_name, array $values) {
		if(!is_string($table_name) || ($query_body = self::StringFromAssoc($values)) == NULL){
			return NULL;
		}
		return 'INSERT INTO ' . $table_name . ' SET ' . $query_body;
	}
	
	protected static function MakeQuery_SELECT(array $fields_array, array $tables_array, array $where_array = NULL, $orderBy_string = NULL) {
		$where = '';
		if($where_array !== NULL) {
			if(!is_array($where_array)) {
				return NULL;
			}
			$where = ' WHERE ' . self::StringFromArray($where_array, 'AND');
		}
		$order = '';
		if($orderBy_string !== NULL) {
			if(!is_string($orderBy_string)) {
				return NULL;
			}
			$order = ' ORDER BY ' . $orderBy_string;
		}
		self::connect();
		$fields = self::StringFromArray($fields_array, ',');
		$tables = self::StringFromArray($tables_array, ',');
		return 'SELECT ' . $fields . ' from '. $tables . $where . $order;
	}
	
	public static function SELECT(array $fields_array, array $tables_array, array $where_array = NULL, $orderBy_string = NULL) {
		$query = self::MakeQuery_SELECT($fields_array, $tables_array, $where_array, $orderBy_string);
		if(self::$debug)
			echo 'SQL::SELECT: ' . $query.  '<br>';
		if($query === NULL) 
			return NULL;
		if($response = mysqli_query(self::$mysqli, $query)) {
			$array_response = array();
			while($t = mysqli_fetch_assoc($response)) {
				array_push($array_response, $t);
			}
			return $array_response;
		} else {
			if(self::$debug) {
				echo mysqli_error(self::$mysqli);
			}
			return NULL;
		}
	}
	
	public static function INSERT_set($tableName, array $values) {
		$query = self::MakeQuery_INSERT_set($tableName, $values);
		if($query === NULL) 
			return NULL;
		self::connect();
		if(self::$debug)
			echo 'SQL::INSERT: ' . $query . '<br>';
		return mysqli_query(self::$mysqli, $query) ? true : false;
	}
	
	public static function safeEncodeString($string)
	{
		self::connect();
		$string = htmlentities($string, ENT_QUOTES, 'UTF-8');
		//$string = addslashes($string);
		$string = mysqli_real_escape_string(self::$mysqli, $string);
		$string = str_replace('\r\n', '<br>', $string);
		return $string;
	}
}
?>
