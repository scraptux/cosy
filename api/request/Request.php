<?php
namespace request;

class Request {
	private $method;
	private $body;
	private $requestHeader;
	private $requestURI;

	public function __construct() {
		$this->method = $_SERVER['REQUEST_METHOD'];
		$this->body = @file_get_contents('php://input');
		$this->requestHeader = getallheaders();
		$this->requestURI = $_SERVER['REQUEST_URI'];
	}

	public function getMethod() {
		return $this->method;
	}
	public function getBody() {
		return $this->body;
	}
	public function getHeader($header) {
		return isset($this->requestHeader[$header]) ? $this->requestHeader[$header] : '';
	}
	public function getRequestURI() {
		return $this->requestURI;
	}
}
?>