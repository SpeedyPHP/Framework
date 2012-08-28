<?php

use \Speedy\Test;
use \Speedy\Functions;

class Object extends Test {
	
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
		assert($object->data('test.test') == 1);
		assert($object->getSomeDataPoint() == 'data');
		//fecho($object->getSomeDataPoint() . " -> Should be \"data\"");
		
		fecho("Dumping all data");
		fecho($object->data());

		$test =& $this->_test;
		fecho("Populating new test");
		$test->setSomeProp("blah");
		$test->setOtherPrivate('nothing');

		fecho("Attempt to pull data");
		assert($test->someProp() == 'blah');
		assert($test->otherPrivate() == 'Nothing');

		var_dump($test);
		fecho("Test successful");
	}
	
}

class TestObj extends \Speedy\Object {

	public $someProp;

	private $_otherPrivate;
	
	protected $_mixins = [ '\\Speedy\\Controller\\Helper\\Session' => ['alias' => 'Session']];


	protected function setOtherPrivate($value) {
		$this->_otherPrivate = ucfirst($value);
	}

	public function otherPrivate() {
		return $this->_otherPrivate;
	}
}
