<?php
namespace response;

class Response {
	private $body = '';
	private $statuscode = \enum\StatusCodes::OK;
	private $responseHeader = array();

	public function setBody($body) {
		$this->body = $body;
	}

	public function registerHeader($header, $value) {
		$this->responseHeader[$header] = $value;
	}

	public function setStatusCode($code) {
		$this->statuscode = $code;
	}

	public function returnResponse() {
		http_response_code($this->statuscode);
		foreach($this->responseHeader as $headerField => $value) {
			header($headerField.':'.$value);
		}
		return $this->body;
	}
}
?>