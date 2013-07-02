<?php 
namespace Speedy\Utility;


class Logger {
		
	private static $_type;
	
	private static $_instance;
	
	
	private static function init() {
		if (self::$_instance !== null) {
			// TODO: Throw error
		}
		
		if (self::$_type == null) self::setType('console');

		switch (self::$_type) {
			case 'console':
				self::$_instance = new \Speedy\Utility\Logger\Console();
				break;
				
			default:
				// TODO: Throw error
		}
		
		return;
	}
	
	public static function instance() {
		if (self::$_instance == null) {
			self::init();
		}
		
		return self::$_instance;
	}
	
	public static function setType($name) {
		self::$_type = $name;
	}
	
	public static function debug($msg) {
		$instance	= self::instance();
		$instance->debug($msg);
	}
	
	public static function info($msg) {
		$instance	= self::instance();
		$instance->info($msg);
	}
	
	public static function fatal($msg) {
		$instance	= self::instance();
		$instance->fatal($msg);
	}
	
}

?>
