<?php
require_once SPEEDY_PATH . "Loader.php";

import('speedy.test');
import('speedy.router.routes.match'); // import the class
import('speedy.request');

use Speedy\Router\Routes\Match;
use Speedy\Request;

class RouteMatch extends \Speedy\Test {
	
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
		  
	}
	
	public function test() {
		$request	= new Request();
		
		output("Starting the first test");
		$route1 = new Match(array( "/testcontroller/:id" => 'test#show', 'on' => 'GET' ));
		if ($route1->match($request)) {
			output("Route matches");
			print_r($route1->getRoute($request));
		} else {
			output("No route");
		}
		
		output("Starting the second test");
		$route2 = new Match(array( "/" => 'test#edit', 'on' => 'GET' ));
		$_REQUEST["url"]			= "";
		$request->addData($_SERVER);
		$request->addParams($_REQUEST);
		$request->parseUri();
		if ($route2->match($request)) {
			output("Matched - index route");
			print_r($route2->getRoute($request));
		} else {
			output("Failed!");
		}
		
		output("Starting the third test");
		$_REQUEST["url"]			= "/";
		$request->addData($_SERVER);
		$request->addParams($_REQUEST);
		$request->parseUri();
		if ($route2->match($request)) {
			output("Matched - index route");
			print_r($route2->getRoute($request));
		} else {
			output("Failed!");
		}
	}
	
}
