<?php 
namespace Speedy;

import("speedy.router.exception");
import('speedy.request');

use \Speedy\Http\Exception as HttpException;
use \Speedy\Asset;

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
	 * @var Speedy\Request
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
	 * @return Speedy\Router
	 */
	static public function instance() {
		if (self::$_instance == null) {
			self::init();
		}
		
		return self::$_instance;
	}
	
	/**
	 * Push route into the stack
	 * @param \Speedy\Router\Routes\Route $route
	 */
	static public function pushRoute($route) {
		$self	= self::getInstance();
		return $self->addRoute($route);
	}
	
	/**
	 * Adds route to router
	 * @param Speedy\Route $route
	 */
	public function addRoute(\Speedy\Router\Routes\Base $route) {
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
		
		foreach ($routes as $route) {
			if ($match) {
				continue;
			}
			
			if ($route->match($request)) {
				$match = true; 
				$this->_setMatchedRoute($route->route());
				
				break;
			}
		}
		
		if ($match === false) {
			$asset	= Asset::instance();
			if ($asset->has($this->request()->scriptName()) !== false) {
				$asset->render();
				exit;
			}
			
			throw new HttpException("No route matches request '{$this->request()->url()}' for {$this->request()->method()}");
			
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
	 * @param \Speedy\Request $request
	 */
	public function setRequest(\Speedy\Request &$request) {
		$this->_request	=& $request;
		return $this;
	}
	
	/**
	 * Getter for request
	 * @return \Speedy\Request
	 */
	public function request() {
		if (!$this->_request && !($this->_request instanceof \Speedy\Request)) {
			throw new Exception('Request is null or not instance of \\Speedy\\Request');
		}
		
		return $this->_request;
	}
	
}

?>