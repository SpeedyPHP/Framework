<?php 
namespace Speedy\Router;


use Speedy\Router;
use Speedy\Router\Routes\Resource;
use Speedy\Router\Routes\Match;
use Speedy\Router\Routes\Regex;
use Speedy\Object;
use Speedy\Utility\Inflector;

class Draw extends Object {
	const GET	= "GET";
	const POST	= "POST";
	const PUT	= "PUT";
	const DELETE= "DELETE";
	
	const NS_CLEAN	= 1;
	const NS_MEMBER	= 2;
	const NS_CONTROLLER = 3;
	const NS_HELPER = 4;
	
	const TYPE_RESOURCE = 1;
	const TYPE_NS	= 2;
	
	const NullActionType = 0;
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
	
	/**
	 * The current type
	 * @var integer
	 */
	private $_currentType;
	
	
	
	
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
		$alias 	= isset($options['as']) ? $options['as'] : $name;
		$member	= $this->buildHelper($alias, true);
		$col	= $this->buildHelper($alias);
		$base	= $this->buildBase($alias, true);
		$controller	= $this->buildController($name); 
		
		$only	= (isset($options['only'])) ? $options['only'] : null;
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
				'type'	=> self::CollectionActionType,
				'helper'=> "%s_url"
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
				'type'	=> self::MemberActionType, 
				'helper'=> "%s_path"
			],
			'destroy'	=> [
				'baseSuffix'=> '/:id', 
				'method'=> self::DELETE,
				'type'	=> self::MemberActionType,
				'helper'=> "%s_path"
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
	public function member($closure) {
		$this->setCurrentType(self::MemberActionType);
		$keys	= array_keys($this->_currentNamespace);
		$this->setData('controller', $this->buildController(end($keys)));
		$this->setData('uri_prefix', ':id/');
		$closure();
		$this->unsetData('controller');
		$this->unsetData('uri_prefix');
		$this->setCurrentType(self::NullActionType);
		
		return;
	}
	
	/**
	 * Add collection route for resource
	 * @param string $method
	 * @param string $action
	 * @return object $this
	 */
	public function collection($closure) {
		$this->setCurrentType(self::CollectionActionType);
		
		$keys	= array_keys($this->_currentNamespace);
		$this->setData('controller', $this->buildController(end($keys)));
		
		$closure();
		$this->unsetData('controller');
		$this->setCurrentType(self::NullActionType);
		
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
		return $this->routeFactory($action, self::POST, $options);
	}
	
	/**
	 * Simple get route
	 * @param string $uri
	 * @param string $action
	 * @return object $this
	 */
	public function get($action, $options = []) {
		return $this->routeFactory($action, self::GET, $options);
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
	 * Getter for current type
	 */
	public function currentType() {
		return $this->_currentType;
	}
	
	/**
	 * Gets called on init and draws all routes
	 */
	protected function draw() {}
	
	/**
	 * Sets the router
	 * @param \Speedy\Router $router
	 */
	protected function setRouter(\Speedy\Router $router) {
		$this->_router = $router;
		return $this;
	}
	
	/**
	 * Route factory
	 * @param string $method
	 * @param string $action
	 * @return object $this
	 */
	protected function routeFactory($action, $method, $options = []) {
		$replace = $this->buildHelper("", ($this->currentType() === self::CollectionActionType) ? false : true);
		$controller	= $this->data('controller');
		$prefix	= ($this->hasData('uri_prefix')) ? $this->data('uri_prefix') : '';
		$uri	= $prefix . $action;
		$helperAction	= (strlen($action) > 0) ? $action . '_' : '';
		
		$defaults= array(
					'name' => ($this->currentType() == self::CollectionActionType) ? "{$helperAction}{$replace}url" : "{$helperAction}{$replace}path"
				);
		$params	= array_merge(array(
				$uri	=> "$controller#$action",
				'on'	=> $method
		), $defaults, $options);
		
		return $this->match($params);
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
	protected function rootTo($toString, $params = []) {
		$params	= array_merge(array('^/?$' => $toString, 'name' => 'root_url'), $params);
		return $this->regex($params);
	}
	
	/**
	 * Regex route match
	 * @param array $options
	 */
	protected function regex(array $options) {
		return $this->pushRoute(new Regex($options));
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
		
		foreach ($this->_currentNamespace as $key => $nsType) {
			$singular	= Inflector::singularize($key);
			
			if ($type == self::NS_MEMBER && $nsType == self::TYPE_RESOURCE) {
				$key	.= "/:{$singular}_id";
			} elseif ($type == self::NS_CONTROLLER && $nsType == self::TYPE_RESOURCE) {
				continue;
			} elseif ($this->currentType() == self::MemberActionType && $type == self::NS_HELPER) {
				$key = $singular;
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
		$ns		= $this->currentNamespace('/', self::NS_CONTROLLER);
		if ($ns)
			$return	.= ($name) ? $ns . '/' : $ns;
		
		return $return . $name;
	}
	
	protected function buildHelper($name, $member = false) {
		$return	= '';
		$ns		= $this->currentNamespace('_', self::NS_HELPER);
		if ($ns)
			$return .= $ns . '_';

		if ($member) 
			$return	.= Inflector::singularize(strtolower($name));
		else 
			$return .= $name;
		
		return $return;
	}
	
	private function setCurrentType($type) {
		$this->_currentType = $type;
		return $this;
	}

}


