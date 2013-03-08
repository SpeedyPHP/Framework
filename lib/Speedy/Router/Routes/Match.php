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
		
		if (isset($params['name'])) {
			$this->setName($params['name']);
		}
		
		if (isset($params['as'])) {
			$this->setName("{$params['as']}_path");
		}
		
		$this->setFormat($format);
		$this->setOptions($params);
	}
	
	public function match($request) {
		return $this->compile($request);
	}
	
	public function route() {
		return $this->params();
	}
	
	/**
	 * Compiles format for route checking
	 * @param string $uri
	 * @return boolean
	 */
	protected function compile($request) {
		// Find matches
		$uri	= $request->url();
		if (!$uri || strlen($uri) < 1) $uri = '/';
		$success 	= preg_match_all($this->pattern(), $uri, $matches);
		$base		= array_shift($matches);
		$params		= array( 'ext' => ($request->hasParam('ext')) ? $request->param('ext') : 'html' );
		//\Speedy\Logger::debug(array($uri, $success, $params, $this->pattern()));
	
		// Fail if it doesn't match
		if (!$success) return false;
	
		// Loop the matches to find the token values
		$i = 0;
		foreach($matches as $key => $value) {
			$value = $value[0];
			if ($i < count($this->token())) {
				$part	= $this->token($i);
				$params[$part]	= $value;
				$i++;
			}
		}
	
		// On greedy find remaining variables
		if ($this->greedy() && (strlen($base[0]) < strlen($uri))) {
			$passed	= substr($uri, strlen($base[0]), strlen($uri));
			$params['passed'] = explode('/', $passed);
		}
	
		$this->setParams(array_merge($params, $this->options()));
	
		return true;
	}
	
}

?>