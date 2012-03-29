<?php 
namespace Vzed\Routes;

import('vzed.router');
import('vzed.router.routes.match');
import('vzed.router.routes.route');
import('vzed.router.routes.resource');

use \Vzed\Router;
use \Vzed\Router\Routes\Resource;
use \Vzed\Router\Routes\Match;

abstract class Draw extends Object {
	
	/**
	 * Holds all route instances
	 * @var array of \Vzed\Router\Routes\Route
	 */
	private $_routes	= array();
	
	/**
	 * Holds current router manager instance
	 * @var \Vzed\Router
	 */
	private $_router 	= null;
	
	
	
	public function __construct() {
		$this->_setRouter(Router::getInstance());
	}
	
	/**
	 * Gets called on init and draws all routes
	 */
	abstract protected function draw();
	
	/**
	 * Sets the router
	 * @param \Vzed\Router $router
	 */
	protected function _setRouter(\Vzed\Router &$router) {
		$this->_router =& $router;
		return $this;
	}
	
	/**
	 * Getter for router
	 * @return \Vzed\Router
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
		$resource	= new Resource($name, $options);
		foreach ($resource->getRoutes() as $route) {
			$this->pushRoute($route);
		}
		
		return $this;
	}
	
	/**
	 * Adds match route to stack
	 * @param string $format
	 * @param array $options
	 * @return $this
	 */
	public function match(string $format, array $options = null) {
		return $this->pushRoute(new Match($format, $options));
	}
	
	/**
	 * Push route into router stack
	 * @param \Vzed\Router\Routes\Route $route
	 */
	protected function pushRoute(\Vzed\Router\Routes\Route $route) {
		$this->router()->addRoute($route);
		return $this;
	}
	
	/**
	 * Draw root path to this location
	 * @param array $params
	 */
	protected function rootTo($toString, $params) {
		$params['/'] = $toString;
		return $this->pushRoute(new Match($params));
	} 
}

?>