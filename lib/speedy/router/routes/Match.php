<?php 
namespace Speedy\Router\Routes;

import('speedy.router.routes.route');

class Match extends Route {
	
	/**
	 * Url match router
	 * @param string $format
	 * @param array $options
	 */
	public function __construct($params = array()) {
		reset($params); 
		
		$firstKey	= key($params); 
		$to		= $params[$firstKey]; 
		if (is_int($firstKey)) {
			$format	= $to;
		} elseif (is_string($firstKey) && strpos($to, '#')) {
			$format	= $firstKey; 
			$action	= explode('#', $to); 
			$params['controller']	= array_shift($action);
			$params['action']		= array_shift($action);
		}
		unset($params[$firstKey]);
		
		if ($params['name']) {
			$this->setName($params['name']);
		}
		
		$this->_setFormat($format);
		$this->_setOptions($params);
	}
	
	public function match(\Speedy\Request $request) {
		/*$request	= $request->getParam('request');
		$controller	= $request[0];
	
		return ($controller == $this->getName()) ? true : false;*/
		return $this->_compile($request);
	}
	
	public function getRoute() {
		return $this->getParams();
	}
	
}

?>