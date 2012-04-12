<?php 
namespace Vzed;

import('vzed.object');

class Response extends Object {

	private $_headers	= array();
	
	
	public function __construct() {
		
	}
	
	/**
	 * Setter for header
	 * @param string $name
	 * @param string $value
	 */
	public function setHeader($name, $value = null) {
		if (!$value)
			$this->_headers[]		= $name; 
		else
			$this->_headers[$name]	= $value;
		
		return $this;
	}
	
	/**
	 * prints out the header that has been setup
	 * @return \Vzed\Response
	 */
	public function printHeaders() {
		$headers	= $this->headers();
		
		if (empty($headers)) return $this;
		foreach ($headers as $name => $value) {
			if (is_string($name)) {
				header("$name: $value");
			} else {
				header($name);
			}
		}
		
		return $this;
	}
	
	/**
	 * Getter for headers property
	 * @return array of headers
	 */
	public function headers() {
		return $this->_headers;
	}
	
}

?>