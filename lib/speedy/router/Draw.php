<?php 
namespace Speedy\Router;

import('speedy.router');
import('speedy.router.routes.match');
import('speedy.router.routes.route');
import('speedy.router.routes.resource');

use \Speedy\Router;
use \Speedy\Router\Routes\Resource;
use \Speedy\Router\Routes\Match;
use \Speedy\Object;

abstract class Draw extends Object {
	const GET	= "GET";
	const POST	= "POST";
	const PUT	= "PUT";
	const DELETE= "DELETE";
	
	
	/**
	 * Holds all route instances
	 * @var array of \Speedy\Router\Routes\Route
	 */
	private $_routes	= array();
	
	/**
	 * Holds current router manager instance
	 * @var \Speedy\Router
	 */
	private $_router 	= null;
	
	
	
	public function __construct() {
		$this->_setRouter(Router::instance());
	}
	
	/**
	 * Gets called on init and draws all routes
	 */
	abstract protected function draw();
	
	/**
	 * Sets the router
	 * @param \Speedy\Router $router
	 */
	protected function _setRouter(\Speedy\Router &$router) {
		$this->_router =& $router;
		return $this;
	}
	
	/**
	 * Getter for router
	 * @return \Speedy\Router
	 */
	public function router() {
		return $this->_router;
	}
	
	/**
	 * Adds resource routes
	 * @param string $name
	 * @param array $options
	 * @return $this
	 */
	public function resources($name, array $options = null) {
		$this->pushRoute(new Match(array("/$name" => "$name#index", 	'on' => self::GET)));
		$this->pushRoute(new Match(array("/$name/new" => "$name#new", 	'on' => self::GET)));
		$this->pushRoute(new Match(array("/$name" => "$name#create", 	'on' => self::POST)));
		$this->pushRoute(new Match(array("/$name/:id" => "$name#show", 'on' => self::GET)));
		$this->pushRoute(new Match(array("/$name/:id/edit" => "$name#edit",	'on' => self::GET)));
		$this->pushRoute(new Match(array("/$name/:id" => "$name#update", 		'on' => self::PUT)));
		$this->pushRoute(new Match(array("/$name/:id" => "$name#destroy", 		'on' => self::DELETE)));
		/*$resource	= new Resource($name, $options);
		foreach ($resource->getRoutes() as $route) {
			$this->pushRoute($route);
		}*/
		
		return $this;
	}
	
	/**
	 * Adds match route to stack
	 * @param string $format
	 * @param array $options
	 * @return $this
	 */
	public function match(array $options = null) {
		return $this->pushRoute(new Match($options));
	}
	
	/**
	 * Push route into router stack
	 * @param \Speedy\Router\Routes\Route $route
	 */
	protected function pushRoute(\Speedy\Router\Routes\Route $route) {
		$this->router()->addRoute($route);
		return $this;
	}
	
	/**
	 * Draw root path to this location
	 * @param array $params
	 */
	protected function rootTo($toString, $params) {
		$params	= array_merge(array('/' => $toString), $params);
		return $this->pushRoute(new Match($params));
	} 
}

?>