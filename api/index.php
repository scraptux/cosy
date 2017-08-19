<?php
include_once 'init.php';

$response = new \response\Response();
$request = new \request\Request();
$db = new \database\Database($response);

switch ($request->getMethod()) {
	case 'GET':
		if (!isset($_GET['q'])) {
			$response->badRequest("Missing method");
		}
		switch ($_GET['q']) {
			case 'album':
				if (!isset($_GET['id'])) {
					$response->badRequest("Missing albumId");
				}
				$db->initSpotify();
				$db->spotify->getAlbum($_GET['id']);
				break;
			case 'artist':
				if (!isset($_GET['id'])) {
					$response->badRequest("Missing artistId");
				}
				$db->initSpotify();
				$db->spotify->getArtist($_GET['id']);
				break;
			case 'login':
				if (!isset($_GET['email']) || !isset($_GET['password'])) {
					$response->badRequest("Missing credentials");
				}
				$db->initUser();
				$db->user->login($_GET['email'], $_GET['password']);
				break;
		}
		break;
	case 'POST':
		if (!isset($_REQUEST['q'])) {
			$response->badRequest("Missing method");
		}
		switch ($_REQUEST['q']) {
			case 'createUser':
				if (!isset($_REQUEST['firstname']) || !isset($_REQUEST['lastname']) || !isset($_REQUEST['email']) || !isset($_REQUEST['password'])) {
					$response->badRequest("Missing user information");
				}
				$db->initUser();
				$db->user->create($_REQUEST['firstname'], $_REQUEST['lastname'], $_REQUEST['email'], $_REQUEST['password']);
				break;
		}
		break;
	case 'PUT':
	case 'DELETE':
	case 'PATCH':
}
?>