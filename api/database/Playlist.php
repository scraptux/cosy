<?php
namespace database;

class Playlist {
	private $db;

	public function __construct(&$db) {
		$this->db = $db;
		$this->db->initUser();
	}

	public function addSongs() {
		$userId = $this->db->user->token2userId();
		if (!isset($_REQUEST['playlistId'])) {
			$this->db->response->badRequest("No playlistId given");
		} else if (!isset($_REQUEST['songs'])) {
			$this->db->response->badRequest("No songs specified");
		}

		$playlistId = $_REQUEST['playlistId'];
		$songIds = $_REQUEST['songs'];

		$stmt = $this->db->conn->prepare("SELECT playlistId FROM `playlistSongs` WHERE `playlistSongs`.playlistId = '".$playlistId."'");
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($id);
		$stmt->fetch();
		$rowCount = $stmt->num_rows;


		$insertSongs = "INSERT INTO `playlistSongs` (playlistId, songId, pos) VALUES ";
		foreach ($songIds as $songId) {
			$rowCount++;
			$insertSongs .= "('".$playlistId."','".$songId."','".$rowCount."'),";
		}
		$this->db->conn->query(rtrim($insertSongs, ','));

		$this->db->response->setStatusCode(\enum\StatusCodes::CREATED);
		$this->db->response->registerHeader(\enum\HeaderFields::CONTENT_TYPE, \enum\HeaderFields::JSON);
		$this->db->response->setBody(json_encode(array('id' => $playlistId)));
		echo $this->db->response->returnResponse();
	}

	public function createPlaylist() {
		$userId = $this->db->user->token2userId();
		if (!isset($_REQUEST['name'])) {
			$this->db->response->badRequest("No playlist name given");
		}

		$name = $this->db->conn->real_escape_string($_REQUEST['name']);
		$this->db->conn->query("INSERT INTO `playlists` 
			(`id`, `ownerId`, `name`, `private`) VALUES 
			(NULL, '1', '".$name."', '1')");
		$playlistId = $this->db->conn->insert_id;

		$this->db->response->setStatusCode(\enum\StatusCodes::CREATED);
		$this->db->response->registerHeader(\enum\HeaderFields::CONTENT_TYPE, \enum\HeaderFields::JSON);
		$this->db->response->setBody(json_encode(array('id' => $playlistId)));
		echo $this->db->response->returnResponse();
	}

	public function getPlaylist() {
		$userId = $this->db->user->token2userId();
		if (!isset($_REQUEST['playlistId'])) {
			$this->db->response->badRequest("No playlistId specified");
		}
		$this->db->initSpotify();

		$stmt = $this->db->conn->prepare("SELECT songId, pos FROM `playlistSongs` WHERE `playlistSongs`.playlistId = '".$_REQUEST['playlistId']."'");
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($id, $pos);
		$tracks = [];
		while ($stmt->fetch()) {
			array_push($tracks, array('id' => $id, 'pos' => $pos));
		}
		usort($tracks, "self::comparePos");
		$sorted = [];
		foreach ($tracks as $track) {
			array_push($sorted, $track['id']);
		}
		$this->db->spotify->getTracks($sorted);
	}

	private function comparePos($a, $b) {
		if($a['pos'] == $b['pos']){ return 0 ; }
    	return ($a['pos'] < $b['pos']) ? -1 : 1;
	}
}
?>