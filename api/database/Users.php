<?php
namespace database;

class Users extends Database {
	public function __construct(&$response) {
		parent::__construct($response);
	}

	public function create($first, $last, $mail, $pass) {
		$first = $this->conn->real_escape_string($first);
		$last = $this->conn->real_escape_string($last);
		$mail = $this->conn->real_escape_string($mail);

		$stmt = $this->conn->prepare("SELECT id FROM `users` WHERE `users`.email = '".$mail."' LIMIT 1");
		$stmt->execute();
		$stmt->store_result();
		if ($stmt->num_rows > 0) {	
			$this->response->setStatusCode(\enum\StatusCodes::BAD_REQUEST);
			$this->response->setBody("Email already in use");
			echo $this->response->returnResponse();
			exit;
		}

		$hash = password_hash($pass, PASSWORD_DEFAULT);
		$this->conn->query("INSERT INTO `users` 
			(`id`, `firstname`, `lastname`, `email`, `hash`, `role`) VALUES 
			(NULL, '".$first."', '".$last."', '".$mail."', '".$hash."', '1')");
	}
}
?>