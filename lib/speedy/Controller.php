<?php 
namespace Speedy;

import('speedy.object');
import('speedy.request');
import('speedy.response');
import('speedy.utility.logger');

use \Speedy\Utility\Logger;
use \Speedy\Utility\Inflector;
use \Speedy\Config;
use \Speedy\Http\Exception as HttpException;
use \Speedy\View;
use \Speedy\Session;

class Controller extends Object {
	
	/**
	 * Params from the request
	 * @var array
	 */
	protected $_params	= null;
	
	/**
	 * The request object
	 * @var \Speedy\Request
	 */
	protected $_request;
	
	/**
	 * The response object
	 * @var \Speedy\Response
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
	 * Closures for object renderers
	 * @var Speedy\Object
	 */
	protected $_format	= new Object();
	
	
	
	/**
	 * Base class for all controllers
	 * @param \Speedy\Request $request
	 * @param \Speedy\Response $response
	 */
	public function __construct(\Speedy\Request &$request, \Speedy\Response &$response) {
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
		
		$this->__runFilter('before_filter');
		
		$this->{$action}();
		
		$this->__runFilter('after_filter');
		
		$this->__beforeRender();
		if (!$this->isRendered()) {
			$this->render();
		}
		
	}
	
	/**
	 * Call the before filters
	 * @return void
	 */
	private function __runFilter($filter) {		
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
	 * @return \Speedy\Controller
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
	 * @param \Speedy\Request $request
	 * @return \Speedy\Controller
	 */
	private function setRequest(\Speedy\Request &$request) {
		if (isset($this->_request)) return $this;
		
		$this->_request =& $request;
		return $this;
	}
	
	/**
	 * Getter for request
	 * @return \Speedy\Request
	 */
	protected function request() {
		return $this->_request;
	}
	
	/**
	 * Response setter
	 * @param \Speedy\Response $response
	 * @return \Speedy\Controller
	 */
	private function setResponse(\Speedy\Response &$response) {
		if (isset($this->_response)) return $this;
		
		$this->_response	=& $response;
		return $this;
	}
	
	/**
	 * Getter for response
	 * @return \Speedy\Response
	 */
	protected function &response() {
		return $this->_response;
	}
	
	protected function respondTo($callback)	{
		$callback($this->format());
	}
	
	private function __beforeRender() {
		$ext		= strtolower($this->param('ext'));
		if ($this->format()->{$ext} && $this->format()->{$ext} instanceof Closure) {
			$closure = $this->format()->{$ext};
			$closure();
		}
	}
	
	/**
	 * Render the page
	 * @param string $path
	 */
	protected function render() {
		$args	= func_get_args();
		$path	= (is_string($args[0])) ? array_shift($args) : null;
		$options= (is_array($args[0])) ? array_shift($args) : array();
		
		if ($this->isRendered()) return;
		
		$options	= array_merge(array( 
			'layout' => $this->layout() 
		), $options);
		$controller	= Inflector::underscore($this->param('controller'));
		$ext		= strtolower($this->param('ext'));
		if (!$path) $path	= $controller . DS . Inflector::underscore($this->param('action'));
		
		if (strpos($path, '/') === false) {
			$relPath	= $controller . DS . $path;
		} else {
			$relPath	= $path;
		}
		
		ob_start();
		$rendered = View::instance()
			->setResponse($this->response())
			->setVars($this->tplVars())
			->setData($this->getData())
			->render($relPath, $options, $ext);
		
		$this->response()->setBody(ob_get_contents());
		ob_end_flush();
		
		if (!$rendered) {
			$controller	= Inflector::underscore($this->param('controller'));
			$action		= Inflector::underscore($this->param('action'));
			throw new HttpException("No view found for $controller#$action");
		} else {
			$this->rendered();
		}
	} 
	
	/**
	 * Accessor for format prop
	 * @return Object
	 */
	protected function &format() {
		return $this->_format;
	}
	
	/**
	 * Check if the current action is rendered
	 * @return boolean
	 */
	private function isRendered() {
		return $this->__rendered;
	}
	
	/**
	 * Sets rendered to true
	 * @return \Speedy\Controller
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
	 * 302 Redirect 
	 */
	public function redirectTo($location, $options = array()) {
		if (is_array($location)) {
			$sLocation	= '';
			while ($part = array_shift($location)) {
				if (strlen($sLocation) > 0) {
					$sLocation	.= '/';
				}
				
				if (is_object($location)) {
					$sLocation	.= $this->modelToPath($location);
				} elseif (is_string($location)) {
					$sLocation	.= $location;
				} 
			}
			
			$location	= $sLocation;
		} elseif (is_object($location)) {
			$location	= $this->modelToPath($location);
		}
		
		if (isset($options['notice'])) {
			Session::instance()->write('notice', $options['notice']);
		} 
		
		if (isset($options['status'])) {
			Session::instance()->write('error', $options['status']);
		}
		
		$this->response()
			->setHeader("Location: /$location");
		
	}
	
	/**
 	 * Convert a model to a location string
	 */
	private function modelToPath($model) {
		$class	= get_class($location);
		return Inflector::singularize(strtolower($class)) . "/{$location->id}";
	}

}

?>