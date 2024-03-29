<?php
namespace database;

class Database {
	public $conn;
	public $response;
	private $config;

	public $user;
	public $spotify;
	public $google;
	public $playlist;

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

	public function initGoogle() {
		$this->google = new \database\Google($this->config['developerkey']);
	}

	public function initPlaylist() {
		$this->playlist = new \database\Playlist($this);
	}

	private function createTables() {
		$this->conn->query("CREATE TABLE IF NOT EXISTS `cosy`.`users` 
			( `id` INT NOT NULL AUTO_INCREMENT , `firstname` TEXT NOT NULL , `lastname` TEXT NOT NULL , `email` TEXT NOT NULL , `hash` TEXT NOT NULL , `role` INT NOT NULL , `image` TEXT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB");
		$this->conn->query("CREATE TABLE IF NOT EXISTS `cosy`.`authTokens` 
			( `userId` INT NOT NULL , `token` TEXT NOT NULL ) ENGINE = InnoDB");
		$this->conn->query("CREATE TABLE IF NOT EXISTS `cosy`.`songs` 
			( `id` TEXT NOT NULL , `video` TEXT NOT NULL ) ENGINE = InnoDB");
		$this->conn->query("CREATE TABLE IF NOT EXISTS `cosy`.`playlists` 
			( `id` INT NOT NULL AUTO_INCREMENT , `ownerId` INT NOT NULL , `name` TEXT NOT NULL , `private` BOOLEAN NOT NULL DEFAULT TRUE , PRIMARY KEY (`id`)) ENGINE = InnoDB");
		$this->conn->query("CREATE TABLE IF NOT EXISTS `cosy`.`playlistSongs` 
			( `playlistId` INT NOT NULL , `songId` TEXT NOT NULL , `pos` INT NOT NULL ) ENGINE = InnoDB");
		$this->conn->query("CREATE TABLE IF NOT EXISTS `cosy`.`playlistFollower` 
			( `playlistId` INT NOT NULL , `userId` INT NOT NULL ) ENGINE = InnoDB");
	}
}
?>