<?php 
namespace Speedy\Router;

import('speedy.router');
import('speedy.router.routes.match');
import('speedy.router.routes.base');
//import('speedy.router.routes.resource');

use \Speedy\Router;
use \Speedy\Router\Routes\Resource;
use \Speedy\Router\Routes\Match;
use \Speedy\Object;
use \Speedy\Utility\Inflector;

abstract class Draw extends Object {
	const GET	= "GET";
	const POST	= "POST";
	const PUT	= "PUT";
	const DELETE= "DELETE";
	
	const NS_CLEAN	= 1;
	const NS_MEMBER	= 2;
	
	
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
	 * Gets called on init and draws all routes
	 */
	abstract protected function draw();
	
	/**
	 * Sets the router
	 * @param \Speedy\Router $router
	 */
	protected function setRouter(\Speedy\Router &$router) {
		$this->_router =& $router;
		return $this;
	}
	
	/**
	 * Adds resource routes
	 * @param string $name
	 * @param array $options
	 * @return $this
	 */
	protected function resources($name, array $options = null, $closure = null) {
		$member	= $this->buildHelper($name, true);
		$col	= $this->buildHelper($name);
		$base	= $this->buildBase($name);
		$controller	= $this->buildController($name); 
		
		$this->pushRoute(new Match(array(
			"$base" => "$controller#index", 	
			'on' => self::GET,
			'name'	=> "{$col}_url"
		)));
		$this->pushRoute(new Match(array(
			"$base/new" => "$controller#_new", 	
			'on' => self::GET,
			'name'	=> "new_{$member}_path"
		)));
		$this->pushRoute(new Match(array(
			"$base" => "$controller#create", 	
			'on' => self::POST
		)));
		$this->pushRoute(new Match(array(
			"$base/:id" => "$controller#show", 
			'on' => self::GET,
			'name'	=> "{$member}_path"
		)));
		$this->pushRoute(new Match(array(
			"$base/:id/edit" => "$controller#edit",	
			'on' => self::GET,
			'name'	=> "edit_{$member}_path"
		)));
		$this->pushRoute(new Match(array("$base/:id" => "$controller#update", 		'on' => self::PUT)));
		$this->pushRoute(new Match(array("$base/:id" => "$controller#destroy", 		'on' => self::DELETE)));
		/*$resource	= new Resource($name, $options);
		foreach ($resource->getRoutes() as $route) {
			$this->pushRoute($route);
		}*/
		
		if ($closure) {
			$this->_namespace($name, $closure);
		}
		
		return $this;
	}
	
	protected function member($method, $action) {
		$base	= $this->buildBase();
		
	}
	
	protected function collection($closure) {
		$closure();
		return $this;
	}
	
	protected function get($action) {
		
	}
	
	/**
	 * Adds match route to stack
	 * @param string $format
	 * @param array $options
	 * @return $this
	 */
	protected function match(array $options = null) {
		return $this->pushRoute(new Match($options));
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
	 * Set a namespaced route
	 * @param string $ns
	 * @param closure $closure
	 * @return void
	 */
	protected function _namespace($ns, $closure) {
		$this->setCurrentNamespace($ns);
		$closure();
		$this->resetCurrentNamespace();
		return;
	}
	
	/**
	 * Setter for current namespace
	 * @param string $ns
	 * @return \Speedy\Router\Draw
	 */
	protected function setCurrentNamespace($ns) {
		if (is_array($this->_currentNamespace)) 
			$this->_currentNamespace[]	= $ns;
		else
			$this->_currentNamespace	= array($ns);
		
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
		$array = $this->_currentNamespace;
		if (!$array) return null;
		
		if ($type == self::NS_CLEAN) {
			array_walk($array, function(&$val, $key) {
				if (strpos($val, '/') === false) {
					return;
				}
				
				$v	= explode('/', $val);
				$val= array_shift($v);
				return;
			});
		} elseif ($type == self::NS_MEMBER) {
			array_walk($array, function(&$val, $key) {
				$singular	= Inflector::singularize($val);
				$val	.= "/{$singular}_id";
				return;
			});
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
	
	protected function buildController($name) {
		$return = '';
		$ns		= $this->currentNamespace('/');
		if ($ns)
			$return	.= $ns . '/';
		
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