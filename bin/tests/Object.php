<?php

class Object extends \Speedy\Test {
	
	private $_object;
	
	public function setup() {
		$this->_object = new \Speedy\Object(); 
		$this->_test = new TestObj();
	}
	
	public function test() {
		$object =& $this->_object;
		
		fecho("Populating object with data");
		$object->setData('test.test', 1);
		$object->setSomeDataPoint('data');
		
		fecho("Attempting to pull the data");
		fecho($object->data('test.test') . " -> Should be 1");
		fecho($object->getSomeDataPoint() . " -> Should be \"data\"");
		
		fecho("Dumping all data");
		fecho($object->data());

		$test =& $this->_test;
		fecho("Populating new test");
		$test->setSomeProp("blah");
		$test->setOtherPrivate('nothing');

		fecho("Attempt to pull data");
		fecho($test->someProp() . ' -> Should be "blah"');
		fecho($test->otherPrivate() . ' -> Should be "Nothing"');

		var_dump($test);
	}
	
}

class TestObj extends \Speedy\Object {

	public $someProp;

	private $_otherPrivate;


	protected function setOtherPrivate($value) {
		$this->_otherPrivate = ucfirst($value);
	}

	public function otherPrivate() {
		return $this->_otherPrivate;
	}
}
