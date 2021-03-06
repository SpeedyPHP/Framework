<?php 
namespace Speedy\View;

use Speedy\Object;
use Speedy\View;
use Speedy\Session;
use Speedy\Http\Exception as HttpException;

abstract class Base extends Object {
	
	use \Speedy\View\Helpers\Html;
	use \Speedy\View\Helpers\Inflector;
	use \Speedy\View\Helpers\SqlLog;
	
	/**
	* Reference to controller
	* @var \Speedy\Controller
	*/
	protected $_controller;
	
	/**
	 * Path to the template
	 * @var string
	 */
	protected $_path;
	
	/**
	 * Options for template
	 * @var array
	 */
	protected $_options;
	
	/**
	 * Template variables
	 * @var array
	 */
	protected $_vars;
	
	/**
	 * Content for yields
	 * @var array
	 */
	protected $_yields = array();
	
	protected $_mixins	= array(
		//'\\Speedy\\View\\Helpers\\Html',
		//'\\Speedy\\View\\Helpers\\Inflector',
		//'\\Speedy\\View\\Helpers\\SqlLog'
	);
	
	public $params;
	
	
	/**
	 * View renderer
	 * @param string $path
	 * @param array $options optional
	 */
	public function __construct($path = null, $options = array()) {
		$this->setPath($path)
			->setOptions($options);
	}
	
	/**
	 * Renders a template
	 * @param $path optional
	 */
	abstract public function renderTemplate($path, $vars = []); 
	
	public function render($path, $vars = [], $ext = null) {
		$ns		= \App::instance()->ns();
		//$options	= $this->options();
		//$path		= ($path) ? $path : $this->path();
		$vars	= array_merge($this->vars(), $vars);
		
		if (is_object($path)) {
			$this->set('object', $path);
			$class	= get_class($path);
			$classArr	= explode('\\', $class);
			$path	= strtolower(array_pop($classArr));
		} 

		$cPath = $this->cleanPath($path);
		//return $this->renderTemplate($path, $this->vars());

		if (!isset($ext)) 
			$ext = $this->ext;
		return View::instance()->render($cPath, [], $vars, $ext);
	}
	
	/**
	 * Renders the template to a string
	 * @param string $template optional path to template
	 * @param string $renderer optional
	 * @return string
	 */
	public function toString($template = null, $vars = []) {
		$this->setOption('layout', null);
		
		ob_start();
		$this->render($template, $vars);
		$content	= ob_get_contents();
		ob_end_clean();
		return $content;
	}
	
	/**
	 * Getter for a yield
	 * @param string $name
	 */
	public function yield($name = "__main__") {
		return "\n" . \Speedy\View::instance()->yield($name) . "\n";
	}
	
	/**
	 * Check if content exists for yield
	 * @param string $name
	 * @return boolean
	 */
	public function hasContentFor($name) {
		return View::instance()->hasContentFor($name);
	}
	
	public function contentFor($name, $closure) {
		ob_start();
		$closure();
		$content = ob_get_contents();
		ob_end_clean();
		
		View::instance()->setYield($name, $content);
		return;
	}
	
	public function cleanPath($path) {
		//if (preg_match("#/^_(\w)*/#i", $path, $matches)) 
		//	return $path;
		
		if (($pos = strrpos($path, '/')) !== false) {
			return substr_replace($path, '_', $pos + 1, 0);
		}
		
		$controller = $this->param('controller');
		if (is_array($controller)) $controller = implode('/', $controller);
		
		if (strpos($path, '/') === false && strpos($path, '_') !== 0) {
			$path	= $controller . "/_" . $path;
		} 

		return $path;
	}
	
	public function toPath($name) {
		return (strpos($name, '/')) ? str_replace('/', DS, $name) : $name; 
	}
	
	public function setParams($params) {
		$this->params	= $params;
		return $this;
	}
	
	/**
	 * Alias for param and getter for params property
	 * 
	 * @param optional mixed $name
	 * @return mixed
	 */
	public function params($name = null) {
		return (isset($name)) ? $this->param($name) : $this->params;
	}

	/**
	 * Getter key in params
	 * 
	 * @param mixed $name
	 * @return mixed
	 */
	public function param($name) {
		return $this->__dotAccess($name, $this->params);
	}
	
	/**
	 * Setter for template path
	 * 
	 * @param string $path
	 * @return \Speedy\View\Base
	 */
	public function setPath($path) {
		$this->_path	= $path;
		return $this;
	}
	
	/**
	 * Setter for template vars
	 * @param array $vars
	 * @return \Speedy\View
	 */
	public function setVars(array $vars) {
		$this->_vars	= $vars;
		return $this;
	}
	
	/**
	 * Getter for options
	 * @return array options
	 */
	public function options() {
		return $this->_options;
	}
	
	/**
	 * Get option
	 * @param string $name
	 */
	public function option($name) {
		return (isset($this->_options[$name])) ? $this->_options[$name] : '';
	}
	
	/**
	 * Setter options
	 * @param array $options
	 */
	public function setOptions($options) {
		$this->_options	= $options;
		return $this;
	}
	
	/**
	 * Setter for option
	 * @param string $name
	 * @param mixed $value
	 * @return \Speedy\View\Base
	 */
	public function setOption($name, $value) {
		$this->_options[$name]	= $value;
		return $this;
	}
	
	/**
	 * Session object getter
	 */
	public function session() {
		return Session::instance();
	}
	
	/**
	 * Getter for templatePath
	 * @return string
	 */
	public function path() {
		return $this->_path;
	}
	
	/**
	 * Getter for template variables
	 * @return array
	 */
	public function vars() {
		return $this->_vars;
	}
	
	/**
	 * Setter for template variables
	 * @param string $name
	 * @param mixed $value
	 * @return \Speedy\View
	 */
	public function set($name, $value) {
		$this->_vars[$name]	= $value;
		return $this;
	}
	
	/**
	 * Getter for current url
	 * @return string
	 */
	public function here() {
		return $this->param('url');
	}
	
	/**
	 * Setter for controller
	 * @param unknown_type $controller
	 */
	protected function setController(&$controller) {
		$this->_controller =& $controller;
		return $this;
	}
	
	/**
	 * Getter for the controller
	 * @return \Speedy\Controller
	 */
	protected function controller() {
		return $this->_controller;
	}
	
}
?>
