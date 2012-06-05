<?php 
namespace Speedy\Router\Routes;


class Match extends Base {
	
	/**
	 * Url match router
	 * @param string $format
	 * @param array $options
	 */
	public function __construct($params = array()) {
		reset($params); 
		
		$firstKey	= key($params); 
		$to			= $params[$firstKey]; 
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
		
		$this->setFormat($format);
		$this->setOptions($params);
	}
	
	public function match(\Speedy\Request $request) {
		/*$request	= $request->getParam('request');
		$controller	= $request[0];
	
		return ($controller == $this->getName()) ? true : false;*/
		return $this->compile($request);
	}
	
	public function route() {
		return $this->params();
	}
	
}

?>