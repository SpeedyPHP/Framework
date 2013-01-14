<?php 
namespace Speedy\Utility;

use Speedy\Singleton;
use Speedy\Router;
use Speedy\App;
use Speedy\Utility\Sanitize;

class Links extends Singleton {

	private $_routePaths;
	
	
	
	
	public function __construct() {
		$this->loadRoutes();
	}
	
	/**
	 * Checks if the path is available
	 * @param string $name
	 * @return boolean
	 */
	public function hasRoutePath($name) {
		return isset($this->_routePaths[$name]);
	}
	
	/**
	 * Getter for route path
	 * @param string $name
	 * @return mixed
	 */
	public function routePath($name) {
		return ($this->hasRoutePath($name)) ? $this->_routePaths[$name] : null;
	}
	
	public function __call($name, $args) {
		if (is_object($args[0])) {
			return $this->pathToResource($args[0]);
		}
		
		if ($this->hasRoutePath($name)) {
			return $this->__pathToLink($name, $args);
		}
	}
	
	public function respondsTo($name) {
		if ($this->hasRoutePath($name)) return true;
		return method_exists($this, $name);
	}
		
	public function hasResourcePath($resource) {
		return isset($this->_routePaths[$resource . "_path"]);
	}
	
	public function pathToResource($model) {
		if (!is_object($model)) return null;
		
		$class	= get_class($model);
		$classArr	= explode('\\', $class);
		$resource	= strtolower(array_pop($classArr));
		
		if (!$this->hasResourcePath($resource)) return null;
		
		return $this->__pathToLink($resource . "_path", array($model->id));
	}
	
	public function __pathToLink($name, $args) {
		$path	= $this->routePath($name);
		extract($path);
			
		if (isset($tokens) && count($args) < count($tokens)) {
			throw new \Speedy\Exception\Utility('No route matches ' . $format);
		}
			
		foreach ($tokens as $token) {
			$value	= array_shift($args);
			$format	= str_replace(":{$token}", (is_object($value)) ? $value->id : $value, $format);
		}
			
		if (!empty($args)) {
			$queryParams = [];
			\Speedy\Logger::debug($args);
			foreach ($args as $key => $value) {
				if (is_int($key)) {
					$queryParams[] .= Sanitize::url($key) . "=" . Sanitize::url($value);
				} else {
					$format .= "/{$value}";
				}
			}
			
			if (count($queryParams) > 0) {
				$format .= '?' . implode('&', $queryParams);
			}
		}
		
		return ($this->shortLinks()) ? 
					$format : "/index.php?url=" . substr($format, 1);
	}
	
	public function shortLinks() {
		return App::instance()->config()->get('short_links');
	}
	
	/**
	 * Loads all the route paths
	 */
	private function loadRoutes() {
		$routes	= Router::instance()->routes();
		
		foreach ($routes as $route) {
			$name	= $route->name();
			if (!$name) {
				continue;
			}
				
			$this->pushRoutePath($name, array(
				'format'	=> $route->format(),
				'tokens'	=> $route->token()
			));
		}
	}
	
	/**
	 * Push named helper into path
	 * @param string $name
	 * @return \Speedy\View\Helpers\Html
	 */ 
	private function pushRoutePath($name, $format) {
		if (isset($this->_routePaths[$name])) return $this;
		$this->_routePaths[$name]	= $format;
	
		return $this;
	}
	
}
?>