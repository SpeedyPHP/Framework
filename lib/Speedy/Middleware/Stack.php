<?php
namespace Speedy\Middleware;


use \ArrayAccess;
use \Speedy\Middleware\Base as MiddlewareAbstract;
use \Speedy\Exception\Middleware as MiddlewareException;

class Stack implements \ArrayAccess {
	
	private $_position = 0;
	
	private $_stack = [];
	
	
	
	/**
	 * Construct new stack
	 * @param object $object
	 * @return object $this
	 */
	public function __construct($object = null) {
		if ($object) {
			$this->add($object);
		}
		
		return $this;
	}
	
	public function addFromArray($objects) {
		foreach ($objects as $object) {
			if (is_string($object)) {
				$this->add(new $object($this));
			} elseif (is_object($object)) {
				$this->add($object);
				$this->setStack($this);
			} 
		}
	}
	
	/**
	 * Push object into stack
	 * @param object $object
	 * @return object $this
	 * @throws MiddlewareException
	 */
	public function add($object) {
		array_unshift($this->_stack, $object);
		return $this;
	}
	
	/**
	 * Get next object from stack
	 * @return object derived from MiddlewareAbstract
	 */
	public function next() {
		$this->_position++;
		return $this[$this->_position];
	}
	
	public function run() {
		$this[0]->call();
	}
	
	public function offsetExists($offset) {
		return isset($this->_stack[$offset]);
	}
	
	public function offsetGet($offset) {
		return $this->_stack[$offset];
	}
	
	public function offsetSet($offset, $value) {
		$this->_stack[$offset] = $value;
	}
	
	public function offsetUnset($offset) {
		unset($this->_stack[$offset]);
	}
	
}
?>
