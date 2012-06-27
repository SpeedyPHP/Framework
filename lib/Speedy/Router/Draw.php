<?php 
namespace Speedy\Router;


use \Speedy\Router;
use \Speedy\Router\Routes\Resource;
use \Speedy\Router\Routes\Match;
use \Speedy\Object;
use \Speedy\Utility\Inflector;

class Draw extends Object {
	const GET	= "GET";
	const POST	= "POST";
	const PUT	= "PUT";
	const DELETE= "DELETE";
	
	const NS_CLEAN	= 1;
	const NS_MEMBER	= 2;
	
	const TYPE_RESOURCE = 1;
	const TYPE_NS	= 2;
	
	
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
	
	/**
	 * Holds the current namespace
	 * @var array
	 */
	private $_currentNamespace;
	
	
	
	
	public function __construct() {
		$this->setRouter(Router::instance());
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
	public function resources($name, array $options = null, $closure = null) {
		$member	= $this->buildHelper($name, true);
		$col	= $this->buildHelper($name);
		$base	= $this->buildBase($name, true);
		$controller	= $this->buildController($name); 
		
		$this->pushRoute(new Match(array_merge(array(
				"$base" => "$controller#index", 	
				'on' => self::GET,
				'name'	=> "{$col}_url"
			), $options
		)));
		$this->pushRoute(new Match(array_merge(array(
				"$base/new" => "$controller#_new", 	
				'on' => self::GET,
				'name'	=> "new_{$member}_path"
			), $options
		)));
		$this->pushRoute(new Match(array_merge(array(
				"$base" => "$controller#create", 	
				'on' => self::POST
			), $options
		)));
		$this->pushRoute(new Match(array_merge(array(
				"$base/:id" => "$controller#show", 
				'on' => self::GET,
				'name'	=> "{$member}_path"
			), $options
		)));
		$this->pushRoute(new Match(array_merge(array(
				"$base/:id/edit" => "$controller#edit",	
				'on' => self::GET,
				'name'	=> "edit_{$member}_path"
			), $options
		)));
		$this->pushRoute(new Match(array_merge(array(
				"$base/:id" => "$controller#update", 		
				'on' => self::PUT
			), $options
		)));
		$this->pushRoute(new Match(array_merge(array(
				"$base/:id" => "$controller#destroy", 		
				'on' => self::DELETE
			), $options
		)));
		/*$resource	= new Resource($name, $options);
		foreach ($resource->getRoutes() as $route) {
			$this->pushRoute($route);
		}*/
		
		if ($closure) {
			$this->_namespace($name, $closure, self::TYPE_RESOURCE);
		}
		
		return $this;
	}
	
	/**
	 * Add member route for resource
	 * @param string $method
	 * @param string $action
	 * @return object $this
	 */
	public function member($method, $action) {
		$uri	= $this->buildBase($action, true);
		$controller	= $this->buildController();
		
		return $this->routeFactory($method, $uri, "$controller#$action");
	}
	
	/**
	 * Add collection route for resource
	 * @param string $method
	 * @param string $action
	 * @return object $this
	 */
	public function collection($method, $action) {
		$controller	= $this->buildController();
		$uri	= $this->buildBase($action);
		
		return $this->routeFactory($method, $uri, "$controller#$action");
	}
	
	/**
	 * Simple get route
	 * @param string $uri
	 * @param string $action
	 * @return object $this
	 */
	public function post($uri, $action) {
		$defaults= array();
		$params	= array_merge($defaults, array(
			$uri	=> $action,
			'on'	=> self::POST
		));
	
		return $this->match($params);
	}
	
	/**
	 * Simple get route
	 * @param string $uri
	 * @param string $action
	 * @return object $this
	 */
	public function get($uri, $action) {
		$defaults= array();
		$params	= array_merge($defaults, array(
			$uri	=> $action,
			'on'	=> self::GET
		));
		
		return $this->match($params);
	}
	
	/**
	 * Adds match route to stack
	 * @param string $format
	 * @param array $options
	 * @return $this
	 */
	public function match(array $options = null) {
		$keys	= array_keys($options);
		$uri	= $this->buildBase($keys[0]);
		$route	= array_pop($options);
		
		if (strpos($route, '#') === false) {
			$controller = $this->buildController();
			$route	= "{$controller}#{$route}";
		} 
		
		$params	= array();
		$params[$uri] = $route;
		$params	= array_merge($params, $options);
		
		return $this->pushRoute(new Match($params));
	} 
	
	/**
	 * Set a namespaced route
	 * @param string $ns
	 * @param closure $closure
	 * @return void
	 */
	public function _namespace($ns, $closure, $type = self::TYPE_NS) {
		$this->setCurrentNamespace($ns, $type);
		$closure();
		$this->resetCurrentNamespace();
		return;
	}
	
	/**
	 * Gets called on init and draws all routes
	 */
	protected function draw() {}
	
	/**
	 * Sets the router
	 * @param \Speedy\Router $router
	 */
	protected function setRouter(\Speedy\Router &$router) {
		$this->_router =& $router;
		return $this;
	}
	
	/**
	 * Route factory
	 * @param string $method
	 * @param string $action
	 * @return object $this
	 */
	protected function routeFactory($method, $uri, $action) {
		switch (strtoupper($method)) {
			case self::POST:
				return $this->post($uri, $action);
				break;
				
			case self::GET:
			default:
				return $this->get($uri, $action);
				break;
		}
	}
	
	/**
	 * Push route into router stack
	 * @param \Speedy\Router\Routes\Route $route
	 */
	protected function pushRoute(\Speedy\Router\Routes\Base $route) {
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
	
	/**
	 * Setter for current namespace
	 * @param string $ns
	 * @return \Speedy\Router\Draw
	 */
	protected function setCurrentNamespace($ns, $type = self::TYPE_RESOURCE) {
		if (is_array($this->_currentNamespace)) 
			$this->_currentNamespace[$ns]	= $type;
		else
			$this->_currentNamespace	= array($ns => $type);
		
		return $this;
	}
	
	/**
	 * Reset the current namespace
	 * @return \Speedy\Router\Draw
	 */
	protected function resetCurrentNamespace() {
		if (is_array($this->_currentNamespace))
			array_pop($this->_currentNamespace);
		else
			$this->_currentNamespace	= null;
		
		return $this;
	}
	
	/**
	 * Return string of current namespace
	 * @return string or null if no namespace defined
	 */
	protected function currentNamespace($delim = '/', $type = 0) {
		if (!$this->_currentNamespace) return null;
		$array = array();
		
		foreach ($this->_currentNamespace as $key => $val) {
			$singular	= Inflector::singularize($key);
			
			if ($type == self::NS_MEMBER && $val == self::TYPE_RESOURCE) {
				$key	.= "/:{$singular}_id";
			}
			
			$array[]	= $key;
		}
		
		return implode($delim, $array);
	}
	
	/**
	 * Build the absolute uri from relative uri
	 * @param string $uri
	 * @return string
	 */
	protected function buildBase($uri, $member = false) {
		$return = '/';
		$ns		= $this->currentNamespace('/', ($member) ? self::NS_MEMBER : 0);
		if ($ns) 
			$return .= $ns . '/';
		
		return $return . $uri;
	}
	
	protected function buildController($name = null) {
		$return = '';
		$ns		= $this->currentNamespace('/');
		if ($ns)
			$return	.= ($name) ? $ns . '/' : $ns;
		
		return $return . $name;
	}
	
	protected function buildHelper($name, $member = false) {
		$return	= '';
		$ns		= $this->currentNamespace('_');
		if ($ns)
			$return .= $ns . '_';
		
		if ($member) 
			$return	.= Inflector::singularize(strtolower($name));
		else 
			$return .= $name;
		
		return $return;
	}

}

?>