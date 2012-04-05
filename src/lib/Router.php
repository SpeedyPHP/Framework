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
	private $_matchedRoute = null;
	
	/**
	 * Request object
	 * @var Vzed\Request
	 */
	private $_request;
	
	
	
	static private function init() {
		if (self::$_instance !== null) {
			throw new Router\Exception("Instance already exists!?");
		}

		self::$_instance = new Router; 
		
		return self::$_instance;
	}
	
	/**
	 * Get shared instance of router
	 * @return Vzed\Router
	 */
	static public function instance() {
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
	
	public function routes() {
		return $this->_routes;
	}
	
	/**
	 * Attempts to discover the matching route and return it
	 * @return Object matched route
	 */
	public function route() {
		if ($route = $this->matchedRoute()) return $route; 
		
		$match	= false;
		$routes	= $this->routes();
		$request= $this->request();
		reset($routes);
		
		foreach ($routes as $route) {
			if ($match) {
				continue;
			}
			
			if ($route->match($request)) {
				$match = true; 
				$this->_setMatchedRoute($route->getRoute());
				
				break;
			}
		}
		
		return $this->matchedRoute();
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
	public function matchedRoute() {
		return $this->_matchedRoute;
	}
	
	/**
	 * Gets the routes from app draw class
	 * @param string $name
	 */
	public function draw($name) {
		if (!$name || !is_string($name)) {
			throw new Router\Exception("Router#draw method missing \$name or \$name is not a string");
		}
		
		$drawer	= new $name();
		$drawer->draw();
		
		return;
	}
	
	/**
	 * Setter for request
	 * @param \Vzed\Request $request
	 */
	public function setRequest(\Vzed\Request &$request) {
		$this->_request	=& $request;
		return $this;
	}
	
	/**
	 * Getter for request
	 * @return \Vzed\Request
	 */
	public function request() {
		if (!$this->_request && !($this->_request instanceof \Vzed\Request)) {
			throw new Exception('Request is null or not instance of \\Vzed\\Request');
		}
		
		return $this->_request;
	}
	
}

?>