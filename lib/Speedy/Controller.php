<?php 
namespace Speedy;


use \Speedy\Utility\Logger;
use \Speedy\Utility\Inflector;
use \Speedy\Config;
use \Speedy\Http\Exception as HttpException;
use \Speedy\View;
use \Speedy\Session;
use \Speedy\Utility\Links;

/**
 * SpeedyPHP Controller forms the 'C' in MVC
 *
 * @author Zachary Quintana
 * @since 1.0
 * @package Speedy
 */
class Controller extends Object {
	
	/**
	 * Params from the request
	 * @var array
	 */
	protected $_params	= null;
	
	/**
	 * The request object
	 * @var object \Speedy\Request
	 */
	protected $_request;
	
	/**
	 * The response object
	 * @var object \Speedy\Response
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
	 * @var object Speedy\Object
	 */
	protected $_format;
	
	/**
	 * Links helper
	 * @var object \Speedy\Utility\Links
	 */
	protected $_linksHelper;
	
	
	
	/**
	 * Base class for all controllers
	 * @param object \Speedy\Request $request
	 * @param object \Speedy\Response $response
	 */
	public function __construct(\Speedy\Request &$request, \Speedy\Response &$response) {
		$params	= $request->params();
		$this->_format	= new Object();
		
		$this->setRequest($request)
			->setParams($params)
			->setResponse($response)
			->_loadMixins();
	}
	
	/**
	 * Call to run the controller
	 * @param string $action 
	 */
	public function __run($action) {
		
		$this->__runFilter('beforeFilter');
		
		$this->{$action}();
		
		$this->__runFilter('afterFilter');
		
		$this->__beforeRender();
		if (!$this->isRendered()) {
			$this->render();
		}
		
	}
	
	/**
	 * Getter for all params
	 * @return mixed default null
	 */
	public function params($name = null) {
		return $this->__dotAccess($name, $this->_params);
	}
	
	/**
	 * Getter for template variables
	 * @return array
	 */
	public function tplVars() {
		return $this->_tplVars;
	}
	
	/**
	 * Getter for links helper
	 * @return object \Speedy\Utility\Links
	 */
	public function linksHelper() {
		if (!$this->_linksHelper) {
			$this->_linksHelper	= Links::instance();
		}
		
		return $this->_linksHelper;
	}
	
	public function __call($name, $args) {
		if ($this->linksHelper()->hasRoutePath($name)) {
			return $this->linksHelper()->__pathToLink($name, $args);
		}
		
		return parent::__call($name, $args);
	}
	
	/**
	 * Run filters
	 * @param string filter name
	 * @return void
	 */
	private function __runFilter($filter) {		
		if (empty($this->{$filter})) {
			return;
		}
		$action	= $this->params('action');
		
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
	 * Setter for links helper
	 * @param object \Speedy\Utility\Links $helper
	 * @return object \Speedy\View\Helpers\Html
	 */
	private function setLinksHelper(\Speedy\Utility\Links $helper) {
		$this->_linksHelper	= $helper;
		return $this;
	}
	
	/**
	 * Setter for request
	 * @param object \Speedy\Request $request
	 * @return object \Speedy\Controller
	 */
	private function setRequest(\Speedy\Request &$request) {
		if (isset($this->_request)) return $this;
		
		$this->_request =& $request;
		return $this;
	}
	
	/**
	 * Response setter
	 * @param object \Speedy\Response $response
	 * @return object \Speedy\Controller
	 */
	private function setResponse(\Speedy\Response &$response) {
		if (isset($this->_response)) return $this;
		
		$this->_response	=& $response;
		return $this;
	}
	
	private function __beforeRender() {
		$ext		= strtolower($this->params('ext'));
		if (isset($this->format()->{$ext}) && get_class($this->format()->{$ext}) == 'Closure') {
			$closure = $this->format()->{$ext};
			$closure();
		}
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
	 * @return object \Speedy\Controller
	 */
	private function rendered() {
		$this->__rendered	= true;
		return $this;
	}
	
	/**
 	 * Convert a model to a location string
	 */
	private function modelToPath($model) {
		$class	= get_class($model);
		$classArr	= explode('\\', $class);
		$class	= Inflector::singularize(strtolower(array_pop($classArr)));
		
		return Inflector::singularize(strtolower($class)) . "/{$model->id}";
	}
	
	/**
	 * 302 Redirect 
	 */
	protected function redirectTo($location, $options = array()) {
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
			$location	= $this->linksHelper()->pathToResource($location);
		}
		
		if (isset($options['notice'])) {
			Session::instance()->write('flash.notice', $options['notice']);
		} 
		
		if (isset($options['error'])) {
			Session::instance()->write('flash.error', $options['error']);
		}
		
		$this->response()
			->setHeader("Location", $location)
			->setBody('')
			->printHeaders();
		//debug($this->response());
		//$this->rendered();
		exit;
	} 
	
	/**
	 * Set all params
	 * @param array $params
	 * @return object \Speedy\Controller
	 */
	protected function setParams(&$params) {
		$this->_params	=& $params;
		return $this;
	}
	
	/**
	 * Getter for request
	 * @return object \Speedy\Request
	 */
	protected function request() {
		return $this->_request;
	}
	
	/**
	 * Getter for response
	 * @return object \Speedy\Response
	 */
	protected function &response() {
		return $this->_response;
	}
	
	protected function respondTo($callback)	{
		$callback($this->format());
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
		$controller	= $this->params('controller');
		$action		= Inflector::underscore($this->params('action'));
		$ext		= strtolower($this->params('ext'));
		
		if ($action == '_new')
			$action	= 'new';
		if (!$path) $path	= $controller . DS . $action;
		
		if (strpos($path, '/') === false) {
			$relPath	= $controller . DS . $path;
		} else {
			$relPath	= $path;
		}
		
		ob_start();
		$rendered = View::instance()
			->setResponse($this->response())
			->setVars($this->tplVars())
			->setData($this->data())
			->setParams($this->params())
			->render($relPath, $options, $ext);
		
		$content	= ob_get_contents();
		ob_end_clean();
		
		if (!$rendered) {
			$controller	= Inflector::underscore($this->params('controller'));
			$action		= Inflector::underscore($this->params('action'));
			throw new HttpException("No view found for $controller#$action");
		} else {
			$this->response()->setBody($content);
			$this->rendered();
		}
	} 
	
	/**
	 * Accessor for format prop
	 * @return object
	 */
	protected function &format() {
		return $this->_format;
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

}

?>