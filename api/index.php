<?php
include_once 'init.php';

$response = new \response\Response();
$request = new \request\Request();
$database = new \database\Database($response);
$user = new \database\Users($database);

switch ($request->getMethod()) {
	case 'GET':
		if (!isset($_GET['q'])) {
			$response->badRequest("Missing method");
		}
		switch ($_GET['q']) {
			case 'login':
				if (!isset($_GET['email']) || !isset($_GET['password'])) {
					$response->badRequest("Missing credentials");
				}
				$user->login($_GET['email'], $_GET['password']);
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
				$user->create($_REQUEST['firstname'], $_REQUEST['lastname'], $_REQUEST['email'], $_REQUEST['password']);
				break;
		}
		break;
	case 'PUT':
	case 'DELETE':
	case 'PATCH':
}
?>