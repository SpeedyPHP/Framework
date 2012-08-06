<?php 
namespace Speedy;

class Set extends \ArrayIterator {
	
	public function (mixed $array) {
		if (!is_array($array) && !is_object($array)) {
			$array = [];
		}
		
		return parent::__construct($array);
	}
	
	public function each($closure) {
		foreach ($this as &$value) {
			$closure($value);
		}
		return;
	}
	
	public function each_key($closure) {
		foreach ($this as $key => &$value) {
			$closure($key, $value);
		}
		return;
	}
	
}
?>