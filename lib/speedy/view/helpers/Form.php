<?php 
namespace Speedy\View\Helpers;

use \Speedy\View\Helpers\Html;
use \Speedy\Object;

class Form extends Object {
	
	private $_model;
	
	private $_name;
	
	private $_path;
	
	private $_helper;
	
	
	
	public function __construct($model, \Speedy\View\Helpers\Html &$helper) {
		$this->setHelper($helper);
		
		$class	= get_class($model);
		$classArr	= explode('\\', $class);
		$classArr	= array_slice($classArr, 2);
		
		$this->setName(array_pop($classArr));
		$this->{$this->name()}	= $model;
		
		$actionPath	= \Speedy\Utility\Inflector::pluralize($this->name());
		if (count($classArr)) {
			while ($name = array_pop($classArr)) {
				$actionPath = "{$name}_{$actionPath}";
			}
		}
		$actionPath	= "{$actionPath}_path";
		
		$this->setPath($this->helper()->{$actionPath}());
	}
	
	/**
	 * Text area builder
	 * @param string $name
	 * @param string $content
	 * @param array $attrs
	 */
	public function textArea($name, $content, $attrs = array()) {
		return $this->helper()->textAreaTag($this->formatName($name), $content, $attrs);
	}
	
	/**
	 * Email field builder
	 * @param string $name
	 * @param array $attrs
	 */
	public function email($name, $attrs = array()) {
		return $this->helper()->emailField($this->formatName($name), $attrs);
	}
	
	/**
	 * Url field builder
	 * @param string $name
	 * @param array $attrs
	 */
	public function url($name, $attrs = array()) {
		return $this->helper()->urlField($this->formatName($name), $attrs);
	}
	
	/**
	 * Telephone field builder
	 * @param string $name
	 * @param array $attrs
	 */
	public function telephone($name, $attrs = array()) {
		return $this->helper()->telephoneField($this->formatName($name), $attrs);
	}
	
	/**
	 * Hidden builder
	 * @param string $name
	 * @param string $value
	 * @param array $attrs
	 */
	public function hidden($name, $value, $attrs = array()) {
		return $this->helper()->hiddenFieldTag($this->formatName($name), $value, $attrs);
	}
	
	/**
	 * Password builder
	 * @param string $name
	 * @param array $attrs
	 */
	public function password($name, $attrs = array()) {
		return $this->helper()->passwordField($this->formatName($name), $attrs);
	}
	
	/**
	 * Radio button builder
	 * @param string $name
	 * @param string $value
	 * @param array $attrs
	 */
	public function radioButton($name, $value, $attrs = array()) {
		return $this->helper()->radioButtonTag($this->formatName($name), $value, $attrs);
	}
	
	/**
	 * Checkbox builder
	 * @param string $name
	 * @param array $attrs
	 */
	public function checkBox($name, $attrs = array()) {
		return $this->helper()->checkBoxTag($this->formatName($name), $attrs);
	}
	
	/**
	 * Label builder
	 * @param string $name
	 * @param array $attrs
	 */
	public function label($name, $label = null) {
		return $this->helper()->labelTag($this->formatName($name), ($label !== null) ? $label : $this->helper()->toLabel($name));
	}
	
	/**
	 * Text field builder
	 * @param string $name
	 * @param array $attrs
	 */
	public function textField($name, $attrs = array()) {
		return $this->helper()->textFieldTag($this->formatName($name), $attrs);
	}
	
	/**
	 * Magic method for when method is missing
	 * @param string $name
	 * @param array $args
	 * @throws \Exception
	 * @return mixed
	 */
	public function __call($name, $args) {
		if (method_exists($this->helper(), $name)) {
			return call_user_func_array(array($this->helper(), $name), $args);
		}
	
		throw new \Exception("No method exists " . get_class($this) . "#$name");
	}
	
	/**
	 * Getter for name
	 * @return string
	 */
	public function name() {
		return $this->_name;
	}
	
	/**
	 * Getter for path
	 * @return string
	 */
	public function path() {
		return $this->_path;
	}
	
	/**
	 * Getter for helper
	 * @return \Speedy\View\Helpers\Html
	 */
	public function helper() {
		return $this->_helper;
	}
	
	/**
	 * Format name for field
	 * @param string $name
	 */
	private function formatName($name) {
		return $this->name() . '.' . $name;
	}
	
	/**
	 * Setter for name
	 * @param string $name
	 * @return \Speedy\View\Helpers\Form
	 */
	protected function setName($name) {
		$this->_name = strtolower($name);
		return $this;
	}
	
	/**
	 * Setter for path
	 * @param string $path
	 * @return \Speedy\View\Helpers\Form
	 */
	protected function setPath($path) {
		$this->_path	= $path;
		return $this;
	}
	
	/**
	 * Setter for helper
	 * @param \Speedy\View\Helpers\Html $helper
	 * @return \Speedy\View\Helpers\Form
	 */
	protected function setHelper(\Speedy\View\Helpers\Html &$helper) {
		$this->_helper =& $helper;
		return $this;
	}
	
}
?>