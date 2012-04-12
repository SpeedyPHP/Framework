<?php 
namespace Vzed;

import('vzed.object');
import('vzed.request');
import('vzed.response');
import('vzed.utility.logger');
import('vzed.view');

use \Vzed\Utility\Logger;
use \Vzed\View;

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
	 * Name of the layout
	 * @var string
	 */
	public $layout;
	
	/**
	 * View has been rendered
	 * @var boolean
	 */
	private $__rendered = false;
	
	/**
	 * Template variables
	 * @var array
	 */
	protected $_tplVars	= array();
	
	
	
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
	public function params() {
		return $this->_params;
	}
	
	/**
	 * Getter for param by string key 
	 * @param string $name
	 * @return mixed
	 */
	public function param($name) {
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
		
		$this->response()->printHeaders();
		$return	= $callback($format);
		
		if ($this->isRendered()) return;
		
		if (is_string($return)) {
			echo $return;
			$this->rendered();
		} else {
			$this->render($this->param('action'));
		}
	}
	
	/**
	 * Render the page
	 * @param string $path
	 */
	protected function render($path = null) {
		$options	= array( 'layout' => $this->layout() );
		$view	= new View($this, $options);
		$view->render($path);
	} 
	
	/**
	 * Check if the current action is rendered
	 * @return boolean
	 */
	private function isRendered() {
		return $this->_rendered;
	}
	
	/**
	 * Sets rendered to true
	 * @return \Vzed\Controller
	 */
	private function rendered() {
		$this->__rendered	= true;
		return $this;
	}
	
	/**
	 * Layout setter
	 * @param string $layout
	 */
	protected function setLayout($layout) {
		$this->layout = $layout;
		return $this;
	}
	
	/**
	 * Getter for layout
	 * @return string layout name
	 */
	protected function layout() {
		return $this->layout;
	}
	
	/**
	 * Template variable setter
	 * @param string $name
	 * @param mixed $value
	 */
	protected function set($name, $value) {
		$this->_tplVars[$name]	= $value;
		return $this;
	}
	
	/**
	 * Getter for template variables
	 * @return array
	 */
	public function tplVars() {
		return $this->_tplVars;
	}
	
	/**
	 * Convert mixed value into json representation 
	 * and set headers for reponse
	 * @param mixed $mixed
	 * @return string json representation
	 */
	protected function toJson($mixed) {
		$this->response()
			->setHeader('Cache-Control', 'no-cache, must-revalidate')
			->setHeader('Expires', date('r'))
			->setHeader('Content-Type', 'application/json');
		
		return json_encode($mixed);
	}
}

?>