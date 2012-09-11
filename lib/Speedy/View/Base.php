<?php 
namespace Speedy\View;

use Speedy\Object;
use Speedy\View;
use Speedy\Session;

abstract class Base extends Object {
	
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
		'\\Speedy\\View\\Helpers\\Html',
		'\\Speedy\\View\\Helpers\\Inflector',
		'\\Speedy\\View\\Helpers\\SqlLog'
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
	
	public function render($path = null, $vars = []) {
		$ns			= \App::instance()->ns();
		$options	= $this->options();
		$path		= ($path) ? $path : $this->path();
		$vars		= array_merge($this->vars(), $vars);
		
		if (($partialPath = $this->isPartial($path)) !== false) {
			View::instance()->render($partialPath, $options, $vars);
			return;
		}
		
		if (!file_exists($path)) {
			throw new HttpException('View found not found at ' . $path);
		}
		
		if (isset($options['layout'])) {
			$layout	= 'layouts' . DS . $options['layout'];
			View::instance()->setYield('__main__', $this->toString($path, $vars));
		
			unset($options['layout']);
			View::instance()->render($layout, $options, $vars);
			return;
		} else {
			return $this->renderTemplate($path, $vars);
		}
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
		echo "\n" . \Speedy\View::instance()->yield($name) . "\n";
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
	
	public function isPartial($path) {
		if (preg_match("#/^_(\w)*/#i", $path, $matches)) return $path;
		
		$controller = $this->param('controller');
		if (is_array($controller)) $controller = implode('/', $controller);
		
		if (strpos($path, '/') === false && strpos($path, '_') !== 0) {
			$path	= $controller . "/_" . $path;
		} 
		
		return (View::instance()->findFile($path)) ? $path : false;
	}
	
	public function toPath($name) {
		return (strpos($name, '/')) ? str_replace('/', DS, $name) : $name; 
	}
	
	public function setParams(&$params) {
		$this->params	=& $params;
		return $this;
	}
	
	public function param($name) {
		return $this->__dotAccess($name, $this->params);
	}
	
	/**
	 * Setter for template path
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