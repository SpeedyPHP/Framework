<?php 
namespace Speedy;


use Speedy\Config;

class Logger {
	
	use \Speedy\Traits\Singleton;
	
	private $_logger;
	
	private $_loggerObj;
	
	private static $_ready = false;
	
	
	
	public function __construct() {
		$this->setLogger(Config::instance()->get('logger'));
	}
	
	public static function info($msg) {
		if (!self::$_ready) return;
		self::instance()->logger()->debug($msg);
	}
	
	public static function debug($msg) {
		if (!self::$_ready) return;
		self::instance()->logger()->debug($msg);
	}
	
	public static function error($msg) {
		if (!self::$_ready) return;
		self::instance()->logger()->error($msg);
	}
	
	public static function fatal($msg) {
		if (!self::$_ready) return;
		self::instance()->logger()->fatal($msg);
	}
	
	public static function warn($msg) {
		if (!self::$_ready) return;
		self::instance()->logger()->warn($msg);
	}
	
	public function setLogger($logger) {
		if (!class_exists($logger)) return;
		
		$this->_logger	= $logger;
		self::$_ready	= true;
		return $this;
	}
	
	public function logger() {
		if (!$this->_loggerObj) {
			$class	= $this->_logger;
			$this->_loggerObj = new $class;
		}
		
		return $this->_loggerObj;
	}
	
}
?>