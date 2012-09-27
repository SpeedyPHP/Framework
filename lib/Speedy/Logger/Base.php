<?php 
namespace Speedy\Logger;


abstract class Base {
	
	/**
	 * Adds an entry to the log
	 * @param mixed $msg
	 */
	abstract public function add($msg);
	
	/**
	 * Adds info
	 * @param mixed $msg
	 */
	abstract public function info($msg);
	
	/**
	 * Adds debug
	 * @param mixed $msg
	 */
	abstract public function debug($msg);
	
	/**
	 * Adds error
	 * @param mixed $msg
	 */
	abstract public function error($msg);
	
	/**
	 * Adds fatal
	 * @param mixed $msg
	 */
	abstract public function fatal($msg);
	
	/**
	 * Adds warning
	 * @param mixed $msg
	 */
	abstract public function warn($msg);
	
}
?>