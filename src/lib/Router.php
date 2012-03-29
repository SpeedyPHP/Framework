<?php 
namespace Vzed;

import("vzed.router.exception");
import('vzed.request');

class Router extends Object {
	/**
	 * Instance of self
	 */
	private static $_instance = null;
	
	/**
	 * Holds all routes to be checked at runtime
	 * @var array of Routes
	 */
	private $_routes = array();
	
	/**
	 * Current matched route
	 * @var 
	 */
	private $_matchedRoute;
	
	
	static private function init() {
		if (self::$_instance !== null) {
			throw new Router\Exception("Instance already exists!?");
		}

		self::$_instance = new Router; 
		
		return $this;
	}
	
	/**
	 * Get shared instance of router
	 * @return Vzed\Router
	 */
	static public function getInstance() {
		if (self::$_instance == null) {
			self::init();
		}
		
		return self::$_instance;
	}
	
	/**
	 * Push route into the stack
	 * @param \Vzed\Router\Routes\Route $route
	 */
	static public function pushRoute($route) {
		$self	= self::getInstance();
		return $self->addRoute($route);
	}
	
	/**
	 * Adds route to router
	 * @param Vzed\Route $route
	 */
	public function addRoute(\Vzed\Router\Routes\Route $route) {
		$this->_routes[] = $route;
		return $this;
	}
	
	public function getRoutes() {
		return $this->_routes;
	}
	
	/**
	 * Attempts to discover the matching route and return it
	 * @return Object matched route
	 */
	public function getRoute() {
		if ($route = $this->getMatchedRoute()) return $route; 
		
		$match	= false;
		$routes	= $this->getRoutes();
		$request= App::request();
		reset($routes);
		
		while ($match === false && $route = next($routes)) {
			if ($route->match($request)) {
				$match = true;
				$this->_setMatchedRoute($route->getRoute());
			}
		}
		
		return $this->getMatchedRoute();
	}
	
	/**
	 * Setter for matched route
	 * @param unknown_type $route
	 */
	protected function _setMatchedRoute($route) {
		$this->_matchedRoute = $route;
		return $this;
	}
	
	/**
	 * Getter for matched request
	 * @return
	 */
	public function getMatchedRoute() {
		return $this->_matchedRoute;
	}
}

?>