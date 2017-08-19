<?php
namespace database;

class Spotify {
	private $session;
	private $api;
	private $db;

	public function __construct($id, $secret, &$db) {
		$this->session = new \SpotifyWebAPI\Session(
			$id,
			$secret
		);
		$this->api = new \SpotifyWebAPI\SpotifyWebAPI();
		$this->session->requestCredentialsToken();
		$token = $this->session->getAccessToken();
		$this->api->setAccessToken($token);
		$this->db = $db;
	}

	public function getAlbum($id) {
		$album = $this->api->getAlbum($id);
		$this->returnResults($album);
	}

	public function getArtist($id) {
		$artist = $this->api->getArtist($id);
		$this->returnResults($artist);
	}

	private function returnResults($arr) {
		$this->db->response->setStatusCode(\enum\StatusCodes::OK);
		$this->db->response->registerHeader(\enum\HeaderFields::CONTENT_TYPE, \enum\HeaderFields::JSON);
		$this->db->response->setBody(json_encode($arr));
		echo $this->db->response->returnResponse();
	}
}
?>