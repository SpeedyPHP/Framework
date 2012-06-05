<?php 
namespace Speedy\Router\Routes;


use \Speedy\Router;
use \Speedy\Router\Routes\Match;

class Resource {
	
	const GET	= "GET";
	const POST	= "POST";
	const PUT	= "PUT";
	const DELETE= "DELETE";
	
	protected $_name;
	
	private $_matchedRoute;
	
	private $_routes = array();
	
	
	
	public function __construct($resource, $options = null) {
		if (!is_string($resource) || empty($resource)) {
			throw new Exception("Resource must be defined as string");
		}
		
		$this->_setName($resource);
		$this->_pushRoute(new Match(array("/$resource" => "$resource#index", 	'on' => self::GET)));
		$this->_pushRoute(new Match(array("/$resource/new" => "$resource#new", 	'on' => self::GET)));
		$this->_pushRoute(new Match(array("/$resource" => "$resource#create", 	'on' => self::POST)));
		$this->_pushRoute(new Match(array("/$resource/:id" => "$resource#show", 'on' => self::GET)));
		$this->_pushRoute(new Match(array("/$resource/:id/edit" => "$resource#edit",	'on' => self::GET)));
		$this->_pushRoute(new Match(array("/$resource/:id" => "$resource#update", 		'on' => self::PUT)));
		$this->_pushRoute(new Match(array("/$resource/:id" => "$resource#destroy", 		'on' => self::DELETE)));
	}
	
	public function match(\Speedy\Request $request) {
		/*$matched = false;;
		foreach ($this->getRoutes() as $route) {
			if ($matched) {
				continue;
			}
			
			if (!$route->match($request)) {
				continue;
			} 
			
			$this->_setMatchedRoute($route);
			$matched = true;
			break;
		}
		
		return $matched;*/
	}
	
	public function getRoute() {
		//return $this->getMatchedRoute()->getRoute();
	}
	
	/**
	 * Setter for name
	 * @return Speedy\Router\Route
	 */
	private function _setName($name) {
		$this->_name = $name;
		return $this;
	}
	
	/**
	 * Adds route to routes
	 * @param $route \Speedy\Router\Routes\Route
	 */
	private function _pushRoute(\Speedy\Router\Routes\Route $route) {
		$this->_routes[]	= $route;
		return $this;
	}
	
	/**
	 * Getter for 
	 */
	public function getRoutes() {
		return $this->_routes;
	}
	
	/**
	 * Getter for name
	 */
	public function getName() {
		return $this->_name;
	}
	
	/**
	 * Setter for matched route
	 * @param \Speedy\Router\Routes\Route $route
	 * @return \Speedy\Router\Routes\Resource
	 */
	private function _setMatchedRoute(\Speedy\Router\Routes\Route $route) {
		$this->_matchedRoute	= $route;
		return $this;
	}
	
	/**
	 * Get matched route
	 * @return \Speedy\Router\Routes\Route
	 */
	public function getMatchedRoute() {
		return $this->_matchedRoute;
	}
}

?>