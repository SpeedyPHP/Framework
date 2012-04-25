<?php 
namespace Speedy\Utility\Logger;

use \Speedy\Utility\Logger;

abstract class Base {
	
	const DEBUG	= 0;
	
	const INFO	= 1;
	
	const FATAL	= 2;
	
	
	abstract public function printLn($msg, $type = self::INFO);
	
	public function debug($msg) {
		$this->printLn($msg, self::DEBUG);
	}
	
	public function info($msg) {
		$this->printLn($msg, self::INFO);
	}
	
	public function fatal($msg) {
		$this->printLn($msg, self::FATAL);
	}
	
}

?>