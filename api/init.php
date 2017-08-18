<?php

function autoload($classname) {
	include_once dirname(__FILE__).DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, explode('\\',$classname)).'.php';
}

spl_autoload_register('autoload');

?>