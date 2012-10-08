<?php 
namespace Speedy;


use Speedy\Http\Exception as HttpException;

class Router extends Object {
	
	use \Speedy\Traits\Singleton;
	
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
	
	private $_params = [];
	
	
	
	public function __construct() {
		$this->setParam('url', $this->request()->url());
	}
	
	/**
	 * Push route into the stack
	 * @param \Speedy\Router\Routes\Route $route
	 */
	static public function pushRoute($route) {
		$self	= self::instance();
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
			throw new HttpException("No route matches request '{$this->request()->scriptName()}' for {$this->request()->method()}");
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
	public function setRequest(\Speedy\Request $request) {
		$this->_request	= $request;
		return $this;
	}
	
	/**
	 * Getter for request
	 * @return \Speedy\Request
	 */
	public function request() {
		if (!$this->_request) {
			$this->_request = Request::instance();
		}
		
		return $this->_request;
	},
	
	private function setParam($name, $value) {
		$this->_params[$name]	= $value;
		return $this;
	}
	
	public function params() {
		return $this->_params;
	}
	
}

?>