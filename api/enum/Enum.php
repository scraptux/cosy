<?php
namespace enum;

abstract class Enum {
	private static function getConstants() {
		$ref = new \ReflectionClass(get_called_class());
		return $ref->getConstants();
	}

	public static function isInList($value) {
		return in_array($value, self::getConstants);
	}
}
?>