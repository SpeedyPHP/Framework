<?php 
namespace Vzed\Router\Routes;

\Vzed\import('vzed.router.routes.route');
\Vzed\import('vzed.router.routes.match');
\Vzed\import('vzed.router');
use \Vzed\Router;

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
		$this->_pushRoute(new Match("/$resource", array('controller' => $resource, 		'action' => 'index', 	'on' => self::GET)));
		$this->_pushRoute(new Match("/$resource/new", array('controller' => $resource, 	'action' => 'new', 		'on' => self::GET)));
		$this->_pushRoute(new Match("/$resource", array('controller' => $resource, 		'action' => 'create', 	'on' => self::POST)));
		$this->_pushRoute(new Match("/$resource/:id", array('controller' => $resource, 	'action' => 'show', 	'on' => self::GET)));
		$this->_pushRoute(new Match("/$resource/:id/edit", array('controller' => $resource, 'action' => 'edit',	'on' => self::GET)));
		$this->_pushRoute(new Match("/$resource/:id", array('controller' => $resource, 	'action' => 'update', 	'on' => self::PUT)));
		$this->_pushRoute(new Match("/$resource/:id", array('controller' => $resource, 	'action' => 'destroy', 	'on' => self::DELETE)));
	}
	
	public function match(\Vzed\Request $request) {
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
	 * @return Vzed\Router\Route
	 */
	private function _setName($name) {
		$this->_name = $name;
		return $this;
	}
	
	/**
	 * Adds route to routes
	 * @param $route \Vzed\Router\Routes\Route
	 */
	private function _pushRoute(\Vzed\Router\Routes\Route $route) {
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
	 * @param \Vzed\Router\Routes\Route $route
	 * @return \Vzed\Router\Routes\Resource
	 */
	private function _setMatchedRoute(\Vzed\Router\Routes\Route $route) {
		$this->_matchedRoute	= $route;
		return $this;
	}
	
	/**
	 * Get matched route
	 * @return \Vzed\Router\Routes\Route
	 */
	public function getMatchedRoute() {
		return $this->_matchedRoute;
	}
}

?>