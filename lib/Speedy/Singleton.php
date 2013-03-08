<?php 
namespace Speedy;

abstract class Singleton extends Object {
	
	private static $_instances = array();
	
	
	/**
	 * Inits the class as singleton
	 * @throws Exception
	 */
	private static function init() {
		if (self::$_instance !== null) {
			throw new Exception("Singleton $class already instanted");
		}

		self::$_instance	= new $class();
		
		return self::$_instance;
	}
	
	/**
	 * Getter for shared singleton instance
	 */
	final public static function instance() {
		$args	= func_get_args();
		$class	= get_called_class();
		
		if (!isset(self::$_instances[$class])) {
			self::$_instances[$class]	= new $class($args);
		}
		
		return self::$_instances[$class];
	}
	
	/**
	 * Singleton objects should not be cloned.
	 *
	 * @return void
	 */
	final private function __clone() {}
	
	final protected function get_called_class() {
		$backtrace = debug_backtrace();
		return get_class($backtrace[2]['object']);
	}
	
}
?>