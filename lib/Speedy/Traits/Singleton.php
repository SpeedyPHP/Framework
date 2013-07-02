<?php 
namespace Speedy\Traits;

trait Singleton {

	private static $_instance;


	/**
	 * Inits the class as singleton
	 * @throws Exception
	 
	private static function init() {
		if (self::$_instance !== null) {
			throw new Exception("Singleton $class already instanted");
		}

		self::$_instance	= new $class();

		return self::$_instance;
	}*/

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
