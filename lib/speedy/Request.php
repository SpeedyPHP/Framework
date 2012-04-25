<?php 
namespace Speedy;

class Request extends Object {
	
	private $_params;
	
	
	public function __construct() {
		$this->addData($_SERVER);
		$this->addParams($_REQUEST);
		
		$this->parseUri();
	}
	
	/**
	 * Setter params
	 * @param mixed $params
	 * @return Speedy\Request
	 */
	public function addParams($params) {
		if (empty($this->_params)) {
			$this->_params = $params;
		} else {
			foreach ($params as $key => $val) {
				$this->_params[$key] = $val;
			}
		}
		
		return $this;
	}
	
	/**
	 * Accessor for params
	 * @return mixed
	 */
	public function params() {
		return $this->_params;
	} 

	/**
	 * Accessor for param
	 * @param string $name
	 * @return mixed
	 */
	public function param($name) {
		return (isset($this->_params[$name])) ? $this->_params[$name] : null;
	}
	
	/**
	 * Setter for params
	 * @param string $name
	 * @param mixed $value
	 * @return Speedy\Request
	 */
	public function setParam($name, $value) {
		$this->_params[$name] = $value;
		return $this;
	}
	
	/**
	 * Checks if a param exists in params array
	 * @param string $name
	 * @return boolean
	 */
	public function hasParam($name) {
		return (isset($this->_params[$name])) ? true : false;
	}
	
	/**
	 * Getter for method
	 */
	public function method() {
		return $this->getData("REQUEST_METHOD");
	}
	
	/**
	 * Getter for host
	 */
	public function host() {
		return $this->getData('HTTP_HOST');
	}
	
	/**
	 * Getter for Query String
	 */
	public function queryString() {
		return $this->getData('QUERY_STRING');
	}
	
	/**
	 * Getter for URI
	 */
	public function uri() {
		return $this->getData('REQUEST_URI');
	}
	
	/**
	 * Getter for url
	 * @return string
	 */
	public function url() {
		return ($this->hasParam('url')) ? $this->param('url') : '/'; 
	}
	
	public function parseUri() {
		$url = $this->url();
		
		$urlParts	= explode("/", $url);
		$last		= end($urlParts);
		
		if (strpos($last, '.')) {
			$lastParts	= explode('.', $last);
			$lastIndex	= count($urlParts) - 1;
			
			$urlParts[$lastIndex]	= $lastParts[0];
			$this->setParam('ext', $lastParts[1]);
			$this->setParam('url', str_replace('.' . $lastParts[1], '', $url));
		}
		
		$this->setParam('request', ($url !== '/') ? $urlParts : array());
		
		return $this;
	}
}

?>