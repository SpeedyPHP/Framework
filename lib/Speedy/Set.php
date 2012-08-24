<?php 
namespace Speedy;

class Set extends \ArrayIterator {
	
	public function __construct(mixed $array) {
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
	
	public function eachKey($closure) {
		foreach ($this as $key => &$value) {
			$closure($key, $value);
		}
		return;
	}
	
}
?>