<?php
require_once SPEEDY_PATH . "Loader.php";

import('speedy.test');
import('speedy.router.routes.resource'); // import the class
import('speedy.request');

use Speedy\Router\Routes\Resource;
use Speedy\Request;

class RouteResource extends \Speedy\Test {
	
	private $_instance;
	
	public function setup() {
		output("Setting up variables");
		// OVERRIDE $_SERVER to imitate server
		$_SERVER["QUERY_STRING"]	= 'url=controller/action/param.html?and=this';
		$_SERVER["REQUEST_URI"]		= "/controller/action/param.html?and=this";
		$_SERVER["HTTP_HOST"]		= "test.com";
		
		// OVERRIDE $_REQUEST to imitate server
		$_REQUEST["and"]			= "this";
		$_REQUEST["PHPSESSID"]		= "d318f4894469c17090540c972a4398fb";
		
		output("Instanting the Resource Class");
		$this->_instance = new Resource("resource"); 
	}
	
	public function test() {
		$instance 	=& $this->_instance;
		$request	= new Request();
		
		output("Starting the test");
		output("Testing GET routes");
		$_SERVER["REQUEST_METHOD"] 	= 'GET';
		$_REQUEST["url"]			= "resource.html";
		
		$request->addData($_SERVER);
		$request->addParams($_REQUEST);
		$request->parseUri();
		
		if ($instance->match($request)) {
			output("Matched - index route");
			print_r($instance->getRoute());
		} else {
			output("Failed!");
		}
		
		$_REQUEST["url"]			= "resource/new.html";
		$request->addData($_SERVER);
		$request->addParams($_REQUEST);
		$request->parseUri();
		if ($instance->match($request)) {
			output("Matched - new route");
			print_r($instance->getRoute());
		} else {
			output("Failed!");
		}
		
		$_REQUEST["url"]			= "resource/1.html";
		$request->addData($_SERVER);
		$request->addParams($_REQUEST);
		$request->parseUri();
		if ($instance->match($request)) {
			output("Matched - show route");
			print_r($instance->getRoute());
		} else {
			output("Failed!");
		}
		
		$_REQUEST["url"]			= "resource/1/edit.html";
		$request->addData($_SERVER);
		$request->addParams($_REQUEST);
		$request->parseUri();
		if ($instance->match($request)) {
			output("Matched - edit route");
			print_r($instance->getRoute());
		} else {
			output("Failed!");
		}
		
		output("Testing POST routes");
		$_SERVER["REQUEST_METHOD"] 	= 'POST';
		$_REQUEST["url"]			= "resource.html";
		$request->addData($_SERVER);
		$request->addParams($_REQUEST);
		$request->parseUri();
		if ($instance->match($request)) {
			output("Matched - create route");
			print_r($instance->getRoute());
		} else {
			output("Failed!");
		}
		
		output("Testing PUT routes");
		$_SERVER["REQUEST_METHOD"] 	= 'PUT';
		$_REQUEST["url"]			= "resource/1.html";
		$request->addData($_SERVER);
		$request->addParams($_REQUEST);
		$request->parseUri();
		if ($instance->match($request)) {
			output("Matched - update route");
			print_r($instance->getRoute());
		} else {
			output("Failed!");
		}
		
		output("Testing DELETE routes");
		$_SERVER["REQUEST_METHOD"] 	= 'DELETE';
		$_REQUEST["url"]			= "resource/1.html";
		$request->addData($_SERVER);
		$request->addParams($_REQUEST);
		$request->parseUri();
		if ($instance->match($request)) {
			output("Matched - delete route");
			print_r($instance->getRoute());
		} else {
			output("Failed!");
		}
	}
	
}
