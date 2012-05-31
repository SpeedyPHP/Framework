<?php 
namespace Speedy;

import('speedy.object');

class Response extends Object {

	private $_headers	= array();

	private $_headersPrinted	= false;
	
	public $body;
	
	
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
	 * @return \Speedy\Response
	 */
	public function printHeaders() {
		if ($this->_headersPrinted == true) return $this;
		
		$headers	= $this->headers();
		
		if (empty($headers)) return $this;
		foreach ($headers as $name => $value) {
			if (is_string($name)) {
				header("$name: $value");
			} else {
				header($name);
			}
		}
		
		$this->_headersPrinted = true;
		return $this;
	}
	
	/**
	 * Getter for headers property
	 * @return array of headers
	 */
	public function headers() {
		return $this->_headers;
	}
	
	public function setBody($content) {
		$this->body	= $content;
		return $this;
	}
	
	public function body() {
		return $this->body;
	}
	
	public function __toString() {
		$this->printHeaders();
		return $this->body;
	}
	
	public function to_s() {
		return $this->__toString();
	}
	
}

?>