<?php 
namespace Speedy\Traits;

trait Singleton {

	private static $_instance;

	/**
	 * Getter for shared singleton instance
	 */
	final public static function instance() {
		$args	= func_get_args();
		$class	= get_called_class();
		
		if (!isset(self::$_instance)) {
			self::$_instance	= new $class($args);
		}

		return self::$_instance;
	}

	/**
	 * Singleton objects should not be cloned.
	 *
	 * @return void
	 */
	final private function __clone() {
	}

}
?>