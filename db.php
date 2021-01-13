<?php
class Database {

    private static $db;
    private $connection;

    $GLOBALS['config']['sql']['host'] = "127.0.0.1";
    $GLOBALS['config']['sql']['dbname'] = "db";
    $GLOBALS['config']['sql']['username'] = "root";
    $GLOBALS['config']['sql']['password'] = "rootpass";


    private function __construct() {
    	try 
		{
		    $this->connection = new PDO('mysql:host='.$GLOBALS['config']['sql']['host'].';dbname='.$GLOBALS['config']['sql']['dbname'].';', $GLOBALS['config']['sql']['username'], $GLOBALS['config']['sql']['password'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") );
		    $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		    $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false );
		}
        catch (PDOException $e)
		{
			return array(0, $e);
		}
    }
    function __destruct() {
        $this->connection = null;
    }
    public static function getConnection() {
        if (self::$db == null) {
            self::$db = new self();
        }
        return self::$db->connection;
    }
	public static function select($sql, $params) {
		$stmt = self::getConnection()->prepare($sql);
		foreach ($params as $key => $value) {
			if (gettype($value) == "string") 
				$stmt->bindParam($key + 1, $params[$key], PDO::PARAM_STR);
			else if (gettype($value) == "NULL")
				$stmt->bindParam($key + 1, $params[$key], PDO::PARAM_NULL);
			else if (gettype($value) == "boolean") 
				$stmt->bindParam($key + 1, $params[$key], PDO::PARAM_BOOL);
			else if (gettype($value) == "integer") 
				$stmt->bindParam($key + 1, $params[$key], PDO::PARAM_INT);
		}
		try {
			$stmt->execute();
		}
		catch (PDOException $e) {
			return array(0, $e);
		}
		return array (1, $stmt->fetchAll(PDO::FETCH_ASSOC));
	}
	public static function insert($sql, $params) {
		$stmt = self::getConnection()->prepare($sql);
		foreach ($params as $key => $value) {
			if (gettype($value) == "string") 
				$stmt->bindParam($key + 1, $params[$key], PDO::PARAM_STR);
			else if (gettype($value) == "NULL")
				$stmt->bindParam($key + 1, $params[$key], PDO::PARAM_NULL);
			else if (gettype($value) == "boolean") 
				$stmt->bindParam($key + 1, $params[$key], PDO::PARAM_BOOL);
			else if (gettype($value) == "integer") 
				$stmt->bindParam($key + 1, $params[$key], PDO::PARAM_INT);
		}
		try {
			$stmt->execute();
		}
		catch (PDOException $e) {
			return array(0, $e);
		}
		return array(1, self::getConnection()->lastInsertId());
	}
	public static function update($sql, $params) {
		$stmt = self::getConnection()->prepare($sql);
		foreach ($params as $key => $value) {
			if (empty($value))
				$value = '';
			if (gettype($value) == "string")
				$stmt->bindParam($key + 1, $params[$key], PDO::PARAM_STR);
			else
				$stmt->bindParam($key + 1, $params[$key], PDO::PARAM_INT);
		}
	}
	public static function selectRow($sql, $params) {
		$result = self::select($sql, $params);
		if ($result[0] != 1)
			return $result;
		if (isset($result[1][0]))
			return array(1, $result[1][0]);
		else 
			return array(1, array());
	}
}
?>
