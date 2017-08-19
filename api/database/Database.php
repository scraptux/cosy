<?php
namespace database;

class Database {
	public $conn;
	public $response;
	private $config;

	public $user;
	public $spotify;

	public function __construct(&$response) {
		$config = parse_ini_file('config.ini');
		$this->conn = mysqli_connect($config['servername'], $config['username'], $config['password'], $config['dbname']);
		$this->response = $response;
		if($this->conn === false) {
			$response->notFound();
		}
		self::createTables();
		$this->config = $config;
	}

	public function initUser() {
		$this->user = new \database\Users($this);
	}

	public function initSpotify() {
		$this->spotify = new \database\Spotify($this->config['clientid'], $this->config['clientsecret'], $this);
	}

	private function createTables() {
		$this->conn->query("CREATE TABLE IF NOT EXISTS `cosy`.`users` 
			( `id` INT NOT NULL AUTO_INCREMENT , `firstname` TEXT NOT NULL , `lastname` TEXT NOT NULL , `email` TEXT NOT NULL , `hash` TEXT NOT NULL , `role` INT NOT NULL , `image` TEXT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB");
		$this->conn->query("CREATE TABLE `cosy`.`authTokens` 
			( `userId` INT NOT NULL , `token` TEXT NOT NULL ) ENGINE = InnoDB");
	}
}
?>