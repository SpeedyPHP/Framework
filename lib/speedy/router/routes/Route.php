<?php 
namespace Speedy\Router\Routes;

abstract class Route {
	
	protected $_params = array();
	
	protected $_request;
	
	protected $_route;
	
	protected $_format;
	
	protected $_options;
	
	protected $_compiledRoute;
	
	/**
	 * Name for use in layouts
	 * @var string
	 */
	protected $_name;
	
	/**
	 * Flag to determin if the route is greedy
	 * @var boolean
	 */
	protected $_greedy;
	
	/**
	 * Regex pattern for route
	 * @var string
	 */
	protected $_pattern	= null;
	
	/**
	 * Route tokens from format
	 * @var array
	 */
	protected $_tokens = null;
	
	
	
	
	/**
	 * Setter for route
	 */
	//abstract public function setRoute();
	
	/**
	 * Checks if route matches request
	 * @param Speedy\Request $request
	 * @return boolean
	 */
	abstract public function match(\Speedy\Request $request);
	
	/**
	 * Returns route for request
	 * @param Speedy\Request $request
	 */
	abstract public function getRoute();
	
	/**
	 * Getter for name
	 * @return string
	 */
	public function name() {
		return $this->_name;
	}
	
	/**
	 * Setter for name
	 * @param string $name
	 * @return \Speedy\Router\Routes\Route
	 */
	protected function setName($name) {
		$this->_name	= $name;
		return $this;
	}
	
	/**
	 * Setter for request
	 * @param Speedy\Request $request
	 * @return Speedy\Route
	 */
	public function setRequest(\Speedy\Request $request) {
		$this->_request = $request;
		
		return $this;
	}
	
	/**
	 * Getter for request
	 * @return \Speedy\Request
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
		
		// Find matches
		$uri	= $request->url();
		if (!$uri || strlen($uri) < 1) $uri = '/';
		$success 	= preg_match_all($this->pattern(), $uri, $matches);
		$base		= array_shift($matches);
		$params		= array( 'ext' => ($request->hasParam('ext')) ? $request->param('ext') : 'html' );
	
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
	 * @return Speedy\Router\Routes\Route
	 */
	protected function _setParams(array $params) {
		unset($params['on']);
		asort($params);
		
		$this->_params = $params;
		
		return $this;
	}
	
	/**
	 * Setter for greedy
	 * @param boolean $greedy
	 * @return \Speedy\Router\Routes\Route
	 */
	protected function setGreedy($greedy) {
		$this->_greedy	= $greedy;
		return $this;
	}
	
	/**
	 * Getter for greedy
	 * @return boolean
	 */
	public function greedy() {
		return $this->_greedy;
	}
	
	/**
	 * Getter for greedy
	 * @return mixed
	 */
	public function token($index = null) {
		if ($this->_tokens == null) {
			$this->processFormat();
		}
		
		return ($index === null) ? $this->_tokens : $this->_tokens[$index];
	}
	
	/**
	 * Setter for tokens
	 * @param array $tokens
	 * @return Match
	 */
	protected function setTokens(array $token) {
		$this->_tokens	= $token;
		return $this;
	}
	
	/**
	 * Processes the format 
	 * @return \Speedy\Router\Router\Routes\Route
	 */
	protected function processFormat() {
		$format	= preg_quote($this->format(), '#');
		
		// is the format greedy and get tokens
		// then loop matches to build regex match
		// for matching the format to the uri
		$this->setGreedy((strpos($format, '*') === strlen($format) - 1) ? true : false);
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
		if (!$this->greedy()) $regex .= '$';
		$regex	.= '#';
		
		$this->setTokens($tokens)->setPattern($regex);
		return $this;
	}
	
	/**
	 * Setter for pattern
	 * @param string $pattern
	 * @return \Speedy\Router\Routes\Route
	 */
	protected function setPattern($pattern) {
		$this->_pattern	= $pattern;
		return $this;
	}
	
	/**
	 * Getter for pattern
	 * @return string
	 */
	public function pattern() {
		if ($this->_pattern == null) {
			$this->processFormat();
		}
		
		return $this->_pattern;
	}
	
}

?>