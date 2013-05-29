<?php 
namespace Speedy;


use \ReflectionClass;

class Enum {

	public static $labels = [];
	
	/**
	 * Getter for all values in enum
	 * @return array
	 */
	public static function all() {
		$reflect = new ReflectionClass(get_called_class());

		return $reflect->getConstants();
	}

	/**
	 * Getter for all constant names
	 * @return array
	 */
	public static function values() {
		$reflect = new ReflectionClass(get_called_class());

		return array_values($reflect->getConstants());
	}

	/**
	 * Get value at index
	 * @return mixed
	 */
	public static function value($index) {
		return isset(self::$labels[$index]) ? $values[$index] : null;
	}

	/**
	 * Convenience method for value
	 * @see value method
	 */
	public static function _($index) {
		return self::value($index);
	}

}