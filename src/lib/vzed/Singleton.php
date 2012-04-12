<?php 
namespace Vzed;

class Singleton extends Object {
	
	private static $_instance;
	
	
	/**
	 * Inits the class as singleton
	 * @throws Exception
	 */
	private static function init() {
		$class	= get_called_class();
		if (self::$_instance !== null) {
			throw new Exception("Singleton $class already instanted");
		}

		self::$_instance	= new $class();
		
		return self::$_instance;
	}
	
	/**
	 * Getter for shared singleton instance
	 */
	public static function instance() {
		if (self::$_instance == null) {
			self::init();
		}
		
		return self::$_instance;
	}
	
}
?>