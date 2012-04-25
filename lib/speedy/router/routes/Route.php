<?php 
namespace Vzed\Router\Routes;

abstract class Route {
	
	protected $_params = array();
	
	protected $_request;
	
	protected $_route;
	
	protected $_format;
	
	protected $_options;
	
	protected $_compiledRoute;
	
	
	
	/**
	 * Setter for route
	 */
	//abstract public function setRoute();
	
	/**
	 * Checks if route matches request
	 * @param Vzed\Request $request
	 * @return boolean
	 */
	abstract public function match(\Vzed\Request $request);
	
	/**
	 * Returns route for request
	 * @param Vzed\Request $request
	 */
	abstract public function getRoute();
	
	/**
	 * Setter for request
	 * @param Vzed\Request $request
	 * @return Vzed\Route
	 */
	public function setRequest(\Vzed\Request $request) {
		$this->_request = $request;
		
		return $this;
	}
	
	/**
	 * Getter for request
	 * @return \Vzed\Request
	 */
	public function getRequest() {
		if (!$this->_request) {
			throw new Exception("Request not set in route");
		}
		
		return $this->_request;
	}
	
	/**
	* Setter for options
	* @param array $options
	*/
	protected function _setOptions($options) {
		$this->_options = $options;
		return $this;
	}
	
	/**
	 * Getter for options property
	 */
	public function getOptions() {
		return (is_array($this->_options)) ? $this->_options : array();
	}
	
	/**
	 * Getter for a specific options
	 * @param int/string $name
	 */
	public function getOption($name) {
		return (!empty($this->_options[$name])) ? $this->_options[$name] : null;
	}
	
	/**
	 * Setter for format
	 * @param string $format
	 */
	protected function _setFormat($format) {
		$this->_format	= $format;
		return $this;
	}
	
	/**
	 * Getter for format
	 */
	public function format() {
		return $this->_format;
	}
	
	/**
	 * Compiles format for route checking
	 * @param string $uri
	 */
	protected function _compile($request) {
		$on	= $this->getOption('on');
		if ($on && strtolower($on) != strtolower($request->method())) return false; 
		
		//  Set vars uri and format, if they're equal then it's a match! early ofcourse
		$uri	= $request->url();
		if (!$uri || strlen($uri) < 1) $uri = '/';
		$format	= preg_quote($this->format(), '#');
		
		// is the format greedy and get tokens
		// then loop matches to build regex match 
		// for matching the format to the uri
		$greedy	= (strpos($format, '*') === strlen($format) - 1) ? true : false;
		preg_match_all('#:?([A-Za-z0-9_-]+[A-Z0-9a-z]*)#', $format, $matches); 
	
		$tokens	= array();
		$regex	= "#";
		$i = 0;
		foreach($matches[0] as $match) {
			if ($i) $regex	.= '/';
			else $regex .= '^';
				
			// if the part starts with colon then it's a token and add it as such
			if (preg_match("#^:#", $match)) {
				$regex		.= "([A-Za-z0-9_\-]+[A-Z0-9a-z]*)";
				$tokens[]	= substr_replace($match, '', 0, 1);
			} else {
				$regex		.= $match;
			}
	
			$i++;
		}
		// Add the regex end if it's not greedy
		if (!$greedy) $regex .= '$';
		$regex	.= '#';
	
		// Find matches
		$success 	= preg_match_all($regex, $uri, $matches);
		$base		= array_shift($matches);
		$params		= array( 'ext' => ($request->hasParam('ext')) ? $request->param('ext') : 'html' );
	
		// \Vzed\debug("Uri - $uri");
		// \Vzed\debug("Regex - $regex");
		// \Vzed\debug("Success? $success");
		// Fail if it doesn't match
		if (!$success) return false;
	
		// Loop the matches to find the token values
		$i = 0;
		foreach($matches as $key => $value) {
			$value = $value[0];
			if ($i < count($tokens)) {
				$part	= $tokens[$i];
				$params[$part]	= $value;
				$i++;
			}
		}
	
		// On greedy find remaining variables
		if ($greedy && (strlen($base[0]) < strlen($uri))) {
			$passed	= substr($uri, strlen($base[0]), strlen($uri));
			$params['passed'] = explode('/', $passed);
		}
	
		$this->_setParams(array_merge($params, $this->getOptions()));
	
		return true;
	}
	
	/**
	 * Getter for params
	 * @return array params
	 */
	public function getParams() {
		return $this->_params;
	}
	
	/**
	 * Setter for params
	 * @param array $params
	 * @return Vzed\Router\Routes\Route
	 */
	protected function _setParams(array $params) {
		unset($params['on']);
		asort($params);
		
		$this->_params = $params;
		
		return $this;
	}
	
	/**
	 * Setter for tokens
	 * @param array $tokens
	 * @return Match
	 */
	protected function _setTokens(array $token) {
		$this->_tokens	= $token;
		return $this;
	}
}

?>