<?php 
namespace Vzed\Router\Routes;

\Vzed\import('vzed.router.routes.route');

class Match extends Route {
	
	/**
	 * Array of available tokens for route
	 * @var array
	 */
	private $_tokens	= array();
	
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
			$action	= split('#', $to);
			$params['controller']	= array_shift($action);
			$params['action']		= array_shift($action);
		}
		unset($params[$firstKey]);
		
		$this->_setFormat($format);
		$this->_setOptions($params);
	}
	
	public function match(\Vzed\Request $request) {
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