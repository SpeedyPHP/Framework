<?php
require_once SPEEDY_PATH . "Loader.php";
import('speedy.test');

class Object extends \Speedy\Test {
	
	private $_object;
	
	public function setup() {
		\Speedy\import('speedy.object');
		
		$this->_object = new \Speedy\Object(); 
	}
	
	public function test() {
		$object =& $this->_object;
		
		fecho("Populating object with data");
		$object->setData('test.test', 1);
		$object->setSomeDataPoint('data');
		
		fecho("Attempting to pull the data");
		fecho($object->getData('test.test') . " -> Should be 1");
		fecho($object->getSomeDataPoint() . " -> Should be \"data\"");
		
		fecho("Dumping all data");
		fecho($object->getData());
	}
	
}