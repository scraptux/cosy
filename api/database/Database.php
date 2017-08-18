<?php
namespace database;

class Database {
	protected $conn;
	protected $response;

	public function __construct(&$response) {
		$config = parse_ini_file('config.ini');
		$this->conn = mysqli_connect($config['servername'], $config['username'], $config['password'], $config['dbname']);
		$this->response = $response;
		if($this->conn === false) {
			$response->notFound();
		}
		self::createTables();
	}

	private function createTables() {
		$this->conn->query("CREATE TABLE IF NOT EXISTS `cosy`.`users` 
			( `id` INT NOT NULL AUTO_INCREMENT , `firstname` TEXT NOT NULL , `lastname` TEXT NOT NULL , `email` TEXT NOT NULL , `hash` TEXT NOT NULL , `role` INT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB");
	}
}
?>