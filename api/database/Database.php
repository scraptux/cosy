<?php
namespace database;

class Database {
	public static $conn;

	public function __construct() {
		$config = parse_ini_file('config.ini');
		$conn = new mysqli($config['servername'], $config['username'], $config['password'], $config['dbname']);
		if(!$conn) {
			die("DB-Connection failed!");
		}
	}
}
?>