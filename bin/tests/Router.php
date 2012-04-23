<?php
require_once VECTOR_PATH . "Loader.php";

\Vzed\import('vzed.test');				// import the test subclass
\Vzed\import('vzed.router');			// import the class
\Vzed\import('vzed.router.draw');
\Vzed\import('vzed.request');

use \Vzed\Router\Draw as VzedDraw;
use \Vzed\Router as VzedRouter;
use \Vzed\Request;

class Router extends \Vzed\Test {
	
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
		
		$this->_router = VzedRouter::instance();
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

class Routes extends VzedDraw {

	public function draw() {

		// Resource routing example:
		// $this->resources('posts');

		// Match example:
		$this->match(array( '/testcontroller/:id' => 'posts#edit', 'on' => 'GET' ));

		// Root example:
		$this->rootTo('posts#show', array( 'id' => 1 ));

	}

}