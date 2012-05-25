<?php
namespace Speedy\Test;

interface Template {
	
	/**
	 * Sets up the test and returns instance of the test
	 * @return instance of test
	 */
	public function setup();
	
	/**
	 * Performs the test
	 * @return int 0 for successfull completion
	 */
	public function test();

}