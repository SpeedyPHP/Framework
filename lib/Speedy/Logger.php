<?php 
namespace Speedy;


use Speedy\Config;

class Logger {
	
	use \Speedy\Traits\Singleton;
	
	private $_logger;
	
	private $_loggerObj;
	
	public $ready = false;
	
	
	
	public function __construct() {
		//$this->setLogger(Config::read('logger'));
	}
	
	public static function info($msg = '') {
		if (!self::ready()) return;
		self::instance()->logger()->info($msg);
	}
	
	public static function debug($msg = '') {
		if (!self::ready()) return;
		self::instance()->logger()->debug($msg);
	}
	
	public static function error($msg = '') {
		if (!self::ready()) return;
		self::instance()->logger()->error($msg);
	}
	
	public static function fatal($msg = '') {
		if (!self::ready()) return;
		self::instance()->logger()->fatal($msg);
	}
	
	public static function warn($msg = '') {
		if (!self::ready()) return;
		self::instance()->logger()->warn($msg);
	}
	
	public static function ready() {
		return self::instance()->ready;
	}
	
	public function setLogger($logger) {
		if (!class_exists($logger)) return;
		
		$this->_loggerObj	= new $logger;
		$this->ready	= true;
		return $this;
	}
	
	public function logger() {
		if (!$this->_loggerObj) {
			$class	= Config::read('logger');
			$this->setLogger($class);
		}
		
		return $this->_loggerObj;
	}
	
}
?>