<?php 
namespace Speedy;

class Request extends Object {
	
	private $_params;
	
	
	public function __construct() {
		$params	= $_GET;
		$params = array_merge($params, ['files' => $_FILES], $_POST);
		$this->addParams($params);
		$this->addData($_SERVER);
		
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
		return $this->__dotAccess($name, $this->_params);
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
		return $this->__dotIsset($name, $this->_params);
	}
	
	/**
	 * Getter for method
	 */
	public function method() {
		return $this->data("REQUEST_METHOD");
	}
	
	/**
	 * Getter for host
	 */
	public function host() {
		return $this->data('HTTP_HOST');
	}
	
	/**
	 * Getter for Query String
	 */
	public function queryString() {
		return $this->data('QUERY_STRING');
	}
	
	/**
	 * Getter for URI
	 */
	public function uri() {
		return $this->data('REQUEST_URI');
	}
	
	/**
	 * Getter for script name
	 */
	public function scriptName() {
		return $this->data('SCRIPT_NAME');
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
		
		if ($this->hasParam('_method')) {
			$this->setMethod($this->param('_method'));
		}
		
		return $this;
	}
	
	/**
	 * Setter for method
	 * @param string $method
	 * @return \Speedy\Request
	 */
	private function setMethod($method) {
		$this->setData('REQUEST_METHOD', strtoupper($method));
		return $this;
	}
}

?>