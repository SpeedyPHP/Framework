<?php 
namespace Speedy\View;

use Speedy\Object;
use Speedy\View;

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
		'speedy.view.helpers.html',
		'speedy.view.helpers.inflector'
	);
	
	
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
	 * Getter for templatePath
	 * @return string
	 */
	protected function path() {
		return $this->_path;
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
	 * Getter for template variables
	 * @return array
	 */
	protected function vars() {
		return $this->_vars;
	}
	
	/**
	 * Setter for template variables
	 * @param string $name
	 * @param mixed $value
	 * @return \Speedy\View
	 */
	protected function set($name, $value) {
		$this->_vars[$name]	= $value;
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
	
	/**
	 * Renders a template
	 * @param $path optional
	 */
	abstract public function render($path = null);
	
	/**
	 * Renders the template to a string
	 * @param string $template optional path to template
	 * @param string $renderer optional
	 * @return string
	 */
	public function toString($template = null) {
		$this->setOption('layout', null);
		
		ob_start();
		$this->render($template);
		$content	= ob_get_clean();
		ob_end_flush();
		return $content;
	}
	
	/**
	 * Getter for a yield
	 * @param string $name
	 */
	public function yield($name = "__main__") {
		echo \Speedy\View::instance()->yield($name);
	}
	
	public function contentFor($name, $closure) {
		ob_start();
		$closure();
		$content = ob_get_clean();
		ob_end_flush();
		
		View::instance()->setYield($name, $content);
		return;
	}
	
	public function isPartial($path) {
		return (preg_match("#/?_(\w)*/#i", $path, $matches)) ? true : false;
	}
	
	public function toPath($name) {
		return (strpos($name, '/')) ? str_replace('/', DS, $name) : $name; 
	}
	
}
?>