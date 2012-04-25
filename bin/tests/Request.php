<?php
require_once SPEEDY_PATH . "Loader.php";
import('speedy.test');

class Request extends \Speedy\Test {
	
	private $_object;
	
	public function setup() {
		\Speedy\import('speedy.request');
		
		// OVERRIDE $_SERVER to imitate server
		$_SERVER["REQUEST_METHOD"] 	= 'GET';
		$_SERVER["QUERY_STRING"]	= 'url=controller/action/param.html?and=this';
		$_SERVER["REQUEST_URI"]		= "/controller/action/param.html?and=this";
		$_SERVER["HTTP_HOST"]		= "test.com";
		
		// OVERRIDE $_REQUEST to imitate server
		$_REQUEST["url"]			= "controller/action/param.html";
		$_REQUEST["and"]			= "this";
		$_REQUEST["PHPSESSID"]		= "d318f4894469c17090540c972a4398fb";
		
		output("Constructing Request");
		$this->_object = new \Speedy\Request(); 
	}
	
	public function test() {
		$object =& $this->_object;
		
		output("Dump of params: ");
		var_dump($object->getParams());
		output();
		
		output("Dump of data: ");
		var_dump($object->getData());
		output();
		
		output("SERVER Props: ");
		output("Method:\t\t" . $object->method());
		output("Query String:\t" . $object->queryString());
		output("Request URI:\t" . $object->uri());
		output("Host:\t\t" . $object->host() . "\n");
		
		output("REQUEST Props: ");
		output("url:\t\t" . $object->getParam("url"));
		output("and:\t\t" . $object->getParam("and"));
		output("PHPSESSID:\t" . $object->getParam("PHPSESSID"));
		output();
		
		output("PARSED URI: ");
		$request = $object->getParam('request');
		foreach($request as $key => $value) {
			output("$key:\t $value");
		}
		output("Ext:\t " . $object->getParam('ext'));
		output();
	}
	
}