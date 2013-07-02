<?php 
namespace Speedy;

class Set extends \ArrayIterator {
	
	public function each($closure) {
		foreach ($this as &$value) {
			$closure($value);
		}
		return;
	}
	
	public function eachKey($closure) {
		foreach ($this as $key => &$value) {
			$closure($key, $value);
		}
		return;
	}
	
	public function first() {
		return $this[0];
	}
	
	public function length() {
		return $this->count();
	}
	
}
?>
