<?php
require_once VECTOR_PATH . "Loader.php";
\Vzed\import('vzed.test');

class Object extends \Vzed\Test {
	
	private $_object;
	
	public function setup() {
		\Vzed\import('vzed.object');
		
		$this->_object = new \Vzed\Object(); 
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