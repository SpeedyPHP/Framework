<?php
namespace Speedy;

require_once "Loader.php";
import('speedy.object');
import('speedy.test.template');

class Test extends Object implements Test\Template {
	
	public function __construct() {
		
	}
	
	public function _setup() {
		$this->setup();
		return $this;
	}
	
	public function setup() {
		output("You needs to implement setup()");
	}
	
	public function test() {
		output("You needs to implement test()");
		return -1;
	}
	
	public function interpretResults($result) {
		if ($result === -1) {
			output("You can implement interpretResults(\$result) do use or ignore this message");
		}
		
		return;
	}
	
}

