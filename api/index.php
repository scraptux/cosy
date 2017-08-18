<?php
include_once 'init.php';

$response = new \response\Response();

//$db = new \database\Database($response);

$request = new \request\Request();

switch ($request->getMethod()) {
	case 'GET':
	case 'POST':
		if (!isset($_REQUEST['q'])) {
			$response->notFound();
		}
		switch ($_REQUEST['q']) {
			case 'createUser':
				if (!isset($_REQUEST['firstname']) || !isset($_REQUEST['lastname']) || !isset($_REQUEST['email']) || !isset($_REQUEST['password'])) {
					$response->notFound();
				}
				$user = new \database\Users($response);
				$user->create($_REQUEST['firstname'], $_REQUEST['lastname'], $_REQUEST['email'], $_REQUEST['password']);
				break;
		}
	case 'PUT':
	case 'DELETE':
	case 'PATCH':
}




//$response->setBody('TEST');
$response->setStatusCode(\enum\StatusCodes::OK);

echo $response->returnResponse();
?>