<?php
namespace database;

class Users {
	private $db;

	public function __construct(&$db) {
		$this->db = $db;
	}

	public function create($first, $last, $mail, $pass) {
		$first = $this->db->conn->real_escape_string($first);
		$last = $this->db->conn->real_escape_string($last);
		$mail = $this->db->conn->real_escape_string($mail);

		$stmt = $this->db->conn->prepare("SELECT id FROM `users` WHERE `users`.email = '".$mail."' LIMIT 1");
		$stmt->execute();
		$stmt->store_result();
		if ($stmt->num_rows > 0) {	
			$this->db->response->setStatusCode(\enum\StatusCodes::BAD_REQUEST);
			$this->db->response->setBody("Email already in use");
			echo $this->db->response->returnResponse();
			exit;
		}

		$hash = password_hash($pass, PASSWORD_DEFAULT);
		$this->db->conn->query("INSERT INTO `users` 
			(`id`, `firstname`, `lastname`, `email`, `hash`, `role`) VALUES 
			(NULL, '".$first."', '".$last."', '".$mail."', '".$hash."', '1')");

		// TODO:LOGIN HERE
		$this->db->response->setStatusCode(\enum\StatusCodes::CREATED);
		echo $this->db->response->returnResponse();
	}

	public function login($mail, $pass) {
		$mail = $this->db->conn->real_escape_string($mail);

		$stmt = $this->db->conn->prepare("SELECT hash, id
			FROM `users` WHERE `users`.email = '".$mail."' LIMIT 1");
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($hash, $userId);
		$stmt->fetch();
		if ($stmt->num_rows <= 0) {
			$this->db->response->setStatusCode(\enum\StatusCodes::UNAUTHORIZED);
			$this->db->response->setBody("Invalid email");
			echo $this->db->response->returnResponse();
			exit;
		}

		if (password_verify($pass, $hash)) {
			$token = self::fetchToken($userId);
			$this->db->response->setStatusCode(\enum\StatusCodes::OK);
			$this->db->response->registerHeader(\enum\HeaderFields::CONTENT_TYPE, \enum\HeaderFields::JSON);
			$this->db->response->setBody(json_encode(array('token' => $token)));
			echo $this->db->response->returnResponse();
		} else {
			$this->db->response->setStatusCode(\enum\StatusCodes::UNAUTHORIZED);
			$this->db->response->setBody("Invalid password");
			echo $this->db->response->returnResponse();
		}
	}

	public function getUser() {
		$userId = self::token2userId();
		error_log($userId);
		$stmt = $this->db->conn->prepare("SELECT email, firstname, lastname, image FROM `users` WHERE `users`.id = '".$userId."' LIMIT 1");
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($email, $firstname, $lastname, $img);
		$stmt->fetch();
		if ($stmt->num_rows <= 0) {
			$this->db->response->unauthorized("No token provided");
		}
		$this->db->response->setStatusCode(\enum\StatusCodes::OK);
		$this->db->response->registerHeader(\enum\HeaderFields::CONTENT_TYPE, \enum\HeaderFields::JSON);
		$this->db->response->setBody(json_encode(array('id' => $userId,
			'email' => $email,
			'firstname' => $firstname,
			'lastname' => $lastname,
			'img' => $img
		)));
		echo $this->db->response->returnResponse();
	}

	private function token2userId() {
		if (isset($_REQUEST['token'])) {
			$stmt = $this->db->conn->prepare("SELECT userId FROM `authTokens` WHERE `authTokens`.token = '".$_REQUEST['token']."' LIMIT 1");
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($userId);
			$stmt->fetch();
			if ($stmt->num_rows <= 0) {
				$this->db->response->unauthorized("No token provided");
			}
		} else {
			$this->db->response->unauthorized("You need to login first");
		}
		return $userId;
	}

	private function fetchToken($userId) {
		$stmt = $this->db->conn->prepare("SELECT token FROM `authTokens` WHERE `authTokens`.userId = '".$userId."' LIMIT 1");
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($token);
		$stmt->fetch();
		if ($stmt->num_rows <= 0) {
			$token = bin2hex(random_bytes(16));
			$this->db->conn->query("INSERT INTO `authTokens` 
				(`userId`, `token`) VALUES ('".$userId."', '".$token."')");
		}
		return $token;
	}
}
?>