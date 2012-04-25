<?php 
namespace Vzed\View;

use Vzed\Object;

abstract class Base extends Object {

	/**
	* Reference to controller
	* @var \Vzed\Controller
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
	 * View renderer
	 * @param string $path
	 * @param array $options optional
	 */
	public function __construct($path, $options = array()) {
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
	 * @return \Vzed\View\Base
	 */
	protected function setPath($path) {
		$this->_path	= $path;
		return $this;
	}
	
	/**
	 * Setter for template vars
	 * @param array $vars
	 * @return \Vzed\View
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
	 * @return \Vzed\View
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
	protected function setOptions($options) {
		$this->_options	= $options;
		return $this;
	}
	
	/**
	 * Setter for option
	 * @param string $name
	 * @param mixed $value
	 * @return \Vzed\View\Base
	 */
	protected function setOption($name, $value) {
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
	 * @return \Vzed\Controller
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
		return ob_get_clean();
	}
	
}
?>