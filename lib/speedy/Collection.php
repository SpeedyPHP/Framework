<?php 
namespace Speedy;

use \Speedy\Object

class Collection extends Object implements Iterator, ArrayAccess {
	
	private $_position	= 0;
	
	private $_collection;
	
	
	
	public function __construct(array $collection) {
		$this->setCollection($collection)->setPosition(0);
	}
	
	public function current() {
		return $this->_collection[$this->position()];
	}
	
	public function key() {
		return $this->position();
	}
	
	public function next() {
		++$this->_position;
	}
	
	public function rewind() {
		$this->setPosition(0);
	}
	
	public function valid() {
		return isset($this->_collection[$this->position()]);
	}
	
	public function collection() {
		return $this->_collection;
	}
	
	public function position() {
		return $this->_position;
	}
	
	public function offsetExists($offset) {
		return isset($this->_collection[$offset]);
	}
	
	public function offsetGet($offset) {
		return ($this->offsetExists($offset)) ? $this->_collection[$offset] : null;
	}
	
	public function offsetSet($offset, $value) {
		if (is_null($offset)) {
			$this->push($value);
		} else {
			$this->set($offset, $value);
		}
	}
	
	public function offsetUnset($offset) {
		unset($this->_collection[$offset]);
	}
	
	public function each($closure) {
		foreach ($this as &$value) {
			$closure($value);
		}
	}
	
	public function set($key, $value) {
		$this->_collection[$key]	= $value;
		return $this;
	}
	
	public function push($value) {
		$this->_collection[]	= $value;
		return $this;
	}

	protected function setCollection($collection) {
		$this->_collection	= $collection;
		return $this;
	}
	
	protected function setPosition($pos) {
		$this->_position	= $pos;
		return $this;
	}
	
}
?>