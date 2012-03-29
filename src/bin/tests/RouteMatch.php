<?php
require_once VECTOR_PATH . "Loader.php";

Vzed\import('vzed.test');
Vzed\import('vzed.router.routes.match'); // import the class
Vzed\import('vzed.request');

use Vzed\Router\Routes\Match;
use Vzed\Request;

class RouteMatch extends \Vzed\Test {
	
	private $_instance;
	
	public function setup() {
		output("Setting up variables");
		// OVERRIDE $_SERVER to imitate server
		$_SERVER["QUERY_STRING"]	= 'url=controller/action/param.html?and=this';
		$_SERVER["REQUEST_URI"]		= "/controller/action/param.html?and=this";
		$_SERVER["HTTP_HOST"]		= "test.com";
		$_SERVER["REQUEST_METHOD"] 	= 'GET';
		
		// OVERRIDE $_REQUEST to imitate server
		$_REQUEST["and"]			= "this";
		$_REQUEST["PHPSESSID"]		= "d318f4894469c17090540c972a4398fb";
		$_REQUEST["url"]			= "testcontroller/1.html";
		
		output("Instanting the Match Class");
		$this->_instance = new Match(array( "/testcontroller/:id" => 'test#show', 'on' => 'GET' )); 
	}
	
	public function test() {
		$instance 	=& $this->_instance;
		$request	= new Request();
		
		output("Starting the test");
		
		$match = $instance->match($request);
		output($match);
		
		if ($match) {
			output("Route matches");
			$params	= $instance->getRoute();
			print_r($params);
		} else {
			output("No route");
		}
		/*if ($instance->match($request)) {
			output("Matched - index route");
			print_r($instance->getRoute($request));
		} else {
			output("Failed!");
		}*/
	}
	
}