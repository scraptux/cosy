<?php
include_once __DIR__.'/vendor/autoload.php';
include_once 'init.php';

header("Access-Control-Allow-Origin: *");

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
			case 'artistAlbums':
				if (!isset($_GET['id'])) {
					$response->badRequest("Missing artistId");
				}
				$db->initSpotify();
				if (isset($_GET['country'])) {
					$db->spotify->getArtistAlbums($_GET['id'],$_GET['country']);
				} else {
					$db->spotify->getArtistAlbums($_GET['id'],'us');
				}
				break;
			case 'artistTopTracks':
				if (!isset($_GET['id'])) {
					$response->badRequest("Missing artistId");
				}
				$db->initSpotify();
				if (isset($_GET['country'])) {
					$db->spotify->getArtistTopTracks($_GET['id'],$_GET['country']);
				} else {
					$db->spotify->getArtistTopTracks($_GET['id'],'us');
				}
				break;
			case 'artistRelatedArtists':
				if (!isset($_GET['id'])) {
					$response->badRequest("Missing artistId");
				}
				$db->initSpotify();
				$db->spotify->getArtistRelatedArtists($_GET['id']);
				break;
			case 'newReleases':
				$db->initSpotify();
				if (isset($_GET['country'])) {
					$db->spotify->getNewReleases($_GET['country']);
				} else {
					$db->spotify->getNewReleases('us');
				}
				break;
			case 'search':
				if (!isset($_GET['p'])) {
					$response->badRequest("Missing searchQuery[p]");
				}
				$db->initSpotify();
				$db->spotify->search($_GET['p']);
				break;
			case 'topTracks':
				$db->initSpotify();
				$db->spotify->getTopTracks();
				break;
			default:
				$response->badRequest("Unknown method");
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
			case 'login':
				if (!isset($_REQUEST['email']) || !isset($_REQUEST['password'])) {
					$response->badRequest("Missing credentials");
				}
				$db->initUser();
				$db->user->login($_REQUEST['email'], $_REQUEST['password']);
				break;
			case 'getUser':
				$db->initUser();
				$db->user->getUser();
				break;
			default:
				$response->badRequest("Unknown method");
				break;
		}
		break;
	case 'PUT':
	case 'DELETE':
	case 'PATCH':
}
?>