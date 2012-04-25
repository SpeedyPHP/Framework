<?php
require_once SPEEDY_PATH . "Loader.php";

import('speedy.test');				// import the test subclass
import('speedy.router');			// import the class
import('speedy.router.draw');
import('speedy.request');

use \Speedy\Router\Draw as SpeedyDraw;
use \Speedy\Router as SpeedyRouter;
use \Speedy\Request;

class Router extends \Speedy\Test {
	
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
		$_REQUEST["url"]			= "/";
		
		$this->_router = SpeedyRouter::instance();
	}
	
	public function test() {
		$router	= $this->getRouter();
		$router->setRequest(new Request())->draw('Routes');
		
		var_dump($router->route());
		//var_dump($router);
		
	}
	
	public function getRouter() {
		return $this->_router;
	}
	
}

class Routes extends SpeedyDraw {

	public function draw() {

		// Resource routing example:
		// $this->resources('posts');

		// Match example:
		$this->match(array( '/testcontroller/:id' => 'posts#edit', 'on' => 'GET' ));

		// Root example:
		$this->rootTo('posts#show', array( 'id' => 1 ));

	}

}