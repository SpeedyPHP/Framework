<?php 
namespace Vzed;

import('vzed.object');
import('vzed.request');
import('vzed.response');

class Controller extends Object {
	
	/**
	 * Params from the request
	 * @var array
	 */
	protected $_params	= null;
	
	/**
	 * The request object
	 * @var \Vzed\Request
	 */
	protected $_request;
	
	/**
	 * The response object
	 * @var \Vzed\Response
	 */
	protected $_response;
	
	/**
	 * View has been rendered
	 * @var boolean
	 */
	private $__rendered = false;
	
	
	
	/**
	 * Base class for all controllers
	 * @param \Vzed\Request $request
	 * @param \Vzed\Response $response
	 */
	public function __construct(\Vzed\Request &$request, \Vzed\Response &$response) {
		$params	= $request->params();
		
		$this->setRequest($request)
			->setParams($params['request'])
			->setResponse($response);
	}
	
	/**
	 * Call to run the controller
	 * @param string $action 
	 */
	public function __run($action) {
		
		$this->__runFilter('before');
		
		$this->{$action}();
		
		$this->__runFilter('after');
		
	}
	
	/**
	 * Call the before filters
	 * @return void
	 */
	private function __runFilter($filter) {
		$filter	= $filter . "Filter";
		
		if (empty($this->{$filter})) {
			return;
		}
		$action	= $this->param('action');
		
		foreach ($this->{$filter} as $func => $options) {
			if (is_array($options)) {
				if (isset($options['only']) && !in_array($action, $options['only'])) {
					continue;
				}
				
				if (isset($options['except']) && in_array($action, $options['except'])) {
					continue;
				}
				
				$this->{$func}();
			} elseif (is_string($options)) {
				$this->{$options}();
			}
		}
		
		return;
	}
	
	/**
	 * Set all params
	 * @param array $params
	 * @return \Vzed\Controller
	 */
	protected function setParams(&$params) {
		$this->_params	=& $params;
		return $this;
	}
	
	/**
	 * Getter for all params
	 * @return mixed
	 */
	protected function params() {
		return $this->_params;
	}
	
	/**
	 * Getter for param by string key 
	 * @param string $name
	 * @return mixed
	 */
	protected function param($name) {
		return $this->__dotAccess($name, $this->_params);
	}
	
	/**
	 * Setter for request
	 * @param \Vzed\Request $request
	 * @return \Vzed\Controller
	 */
	private function setRequest(\Vzed\Request &$request) {
		if (isset($this->_request)) return $this;
		
		$this->_request =& $request;
		return $this;
	}
	
	/**
	 * Getter for request
	 * @return \Vzed\Request
	 */
	protected function request() {
		return $this->_request;
	}
	
	/**
	 * Response setter
	 * @param \Vzed\Response $response
	 * @return \Vzed\Controller
	 */
	private function setResponse(\Vzed\Response &$response) {
		if (isset($this->_response)) return $this;
		
		$this->_response	=& $response;
		return $this;
	}
	
	/**
	 * Getter for response
	 * @return \Vzed\Response
	 */
	protected function response() {
		return $this->_response;
	}
	
	protected function respondTo($callback)	{
		$format = $this->param('ext');
		if (empty($format)) $format	= 'html';
		
		$callback($format);
	}
	
}

?>