<?php
namespace enum;

class StatusCodes extends Enum {
	const OK = 200;

	const BAD_REQUEST = 400;
	const NOT_FOUND = 404;

	const INTERNAL_SERVER_ERROR = 500;
	const NOT_IMPLEMENTED = 501;
	const BAD_GATEWAY = 502;
	const SERVICE_UNAVAILABLE = 503;
}
?>