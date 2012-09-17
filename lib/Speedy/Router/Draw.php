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
	
	const MemberActionType = 1;
	const CollectionActionType = 2;
	
	
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
	public function resources($name, $options = null, $closure = null) {
		$member	= $this->buildHelper($name, true);
		$col	= $this->buildHelper($name);
		$base	= $this->buildBase($name, true);
		$controller	= $this->buildController($name); 
		
		$only	= (isset($options['only']) && count($options['only']) > 0) ? $options['only'] : null;
		$except	= (isset($options['except']) && count($options['except']) > 0) ? $options['except'] : null;
		$defaultActions= [
			'index' => [
				'method'=> self::GET,
				'helper'=> "%s_url",
				'type'	=> self::CollectionActionType
			], 
			'new'	=> [
				'action'	=> '_new',
				'baseSuffix'=> '/new', 
				'method'=> self::GET,
				'helper'=> "new_%s_path",
				'type'	=> self::MemberActionType
			],
			'create'=> [
				'method'=> self::POST,
				'type'	=> self::CollectionActionType
			],
			'show'	=> [
				'baseSuffix'=> '/:id', 
				'method'=> self::GET,
				'helper'=> "%s_path",
				'type'	=> self::MemberActionType
			],
			'edit'	=> [
				'baseSuffix'=> '/:id/edit', 
				'method'=> self::GET,
				'helper'=> "edit_%s_path",
				'type'	=> self::MemberActionType
			],
			'update'=> [
				'baseSuffix'=> '/:id',	
				'method'=> self::PUT,
				'type'	=> self::MemberActionType 
			],
			'destroy'	=> [
				'baseSuffix'=> '/:id', 
				'method'=> self::DELETE,
				'type'	=> self::MemberActionType
			]
		];
		
		/*$resource	= new Resource($name, $options);
		 foreach ($resource->getRoutes() as $route) {
		$this->pushRoute($route);
		}*/
		
		if ($closure) {
			$this->_namespace($name, $closure, self::TYPE_RESOURCE);
		}
		
		foreach ($defaultActions as $action => $settings) {
			if (is_array($only) && !in_array($action, $only)) {
				continue;
			}
			
			if (is_array($except) && in_array($action, $except)) {
				continue;
			}
			
			if (isset($settings['action'])) {
				$action = $settings['action'];
			}
			
			$replace = '';
			if ($settings['type'] === self::CollectionActionType) $replace = $col;
			if ($settings['type'] === self::MemberActionType) $replace = $member;
			if (isset($settings['helper'])) 
				$helper = str_replace('%s', $replace, $settings['helper']);
			
			$uri	= $base;
			if (isset($settings['baseSuffix'])) $uri .= $settings['baseSuffix'];
			$defaults = [
				$uri	=> "{$controller}#{$action}",
				'on'	=> $settings['method'],
				'name'	=> (isset($settings['helper'])) ? $helper : null
			];
			$opts = array_merge($defaults, (is_array($options)) ? $options : []);
			
			$this->pushRoute(new Match($opts));
		}
		
		return $this;
	}
	
	/**
	 * Add member route for resource
	 * @param string $method
	 * @param string $action
	 * @return object $this
	 */
	public function member($action) {
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
	public function collection($closure) {
		$this->setData('controller', $this->buildController());
		$closure();
		$this->unsetData('controller');
		
		return;
	}
	/*public function collection($action) {
		$controller	= $this->buildController();
		$uri	= $this->buildBase($action);
		
		return $this->routeFactory($method, $uri, "$controller#$action");
	}*/
	
	/**
	 * Simple get route
	 * @param string $uri
	 * @param string $action
	 * @return object $this
	 */
	public function post($action, $options = []) {
		$controller	= $this->data('controller');
		
		$defaults= array();
		$params	= array_merge($defaults, $options, array(
			$action	=> "$controller#$action",
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
	public function get($action, $options = []) {
		$controller	= $this->data('controller');
		
		$defaults= array();
		$params	= array_merge($defaults, $options, array(
			$action	=> "$controller#$action",
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
		$route	= array_shift($options);
		
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