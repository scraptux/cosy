<?php
include_once 'init.php';

$request = new \request\Request();

switch ($request->getMethod()) {
	case 'GET':
	case 'POST':
	case 'PUT':
	case 'DELETE':
	case 'PATCH':
}


$response = new \response\Response();

$response->setBody('TEST');
$response->setStatusCode(\enum\StatusCodes::OK);

echo $response->returnResponse();
?>