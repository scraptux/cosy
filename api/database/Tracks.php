<?php
namespace database;

class Tracks {
	private $db;

	public function __construct(&$db) {
		$this->db = $db;
	}

	public function getTrack(&$track) {
		$stmt = $this->db->conn->prepare("SELECT id FROM `songs` WHERE `songs`.id = '".$track->id."' LIMIT 1");
		$stmt->execute();
		$stmt->store_result();
		if ($stmt->num_rows <= 0) {
			$this->db->initGoogle();
			$yt = $this->db->google->getIdentifier($track->name.' '.$track->artists[0]->name);
			$this->db->conn->query("INSERT INTO `songs` (id, video) VALUES ('".$track->id."','".$yt."')");
		}

		$stmt = $this->db->conn->prepare("SELECT video FROM `songs` WHERE id = '".$track->id."' LIMIT 1");
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($yt);
		$stmt->fetch();
		$track->video = $yt;
	}
}
?>