<?php 
namespace Speedy\View\Helpers;


use \Speedy\Object;

class Form extends Object {
	
	use \Speedy\View\Helpers\Html;
	
	private $_model;
	
	private $_name;
	
	private $_path;
	
	private $_helper;
	
	
	
	public function __construct($model) {
		$basepath	= '';
		if (is_array($model)) {
			$basepathArr	= $model;
			$model		= array_pop($basepathArr);
			$basepath	= implode('_', $basepathArr) . '_';
		}
		
		$this->setModel($model);
		
		$class	= get_class($model);
		$classArr	= explode('\\', $class);
		$classArr	= array_slice($classArr, 2);
		
		$this->setName(array_pop($classArr));
		$this->{$this->name()}	= $model;
		
		if ($model->id) {
			$actionPath	= $this->name();
		} else {
			$actionPath	= \Speedy\Utility\Inflector::pluralize($this->name());
		}
		
		if (count($classArr)) {
			while ($name = array_pop($classArr)) {
				$actionPath = "{$name}_{$actionPath}";
			}
		}
		
		if ($model->id) {
			$actionPath	= "{$basepath}{$actionPath}_path";
			$this->setPath($this->{$actionPath}($model->id));
		} else {
			$actionPath	= "{$basepath}{$actionPath}_url";
			$this->setPath($this->{$actionPath}());
		}
	}
	
	/**
	 * Text area builder
	 * @param string $name
	 * @param string $content
	 * @param array $attrs
	 */
	public function textArea($name, $attrs = array()) {
		return $this->textAreaTag($this->formatName($name), $this->model()->{$name}, $attrs);
	}
	
	/**
	 * Email field builder
	 * @param string $name
	 * @param array $attrs
	 */
	public function email($name, $attrs = array()) {
		$attrs['value']	= (isset($this->model()->{$name})) ? $this->model()->{$name} : null;
		return $this->emailField($this->formatName($name), $attrs);
	}
	
	/**
	 * Url field builder
	 * @param string $name
	 * @param array $attrs
	 */
	public function url($name, $attrs = array()) {
		$attrs['value']	= $this->model()->{$name};
		return $this->urlField($this->formatName($name), $attrs);
	}
	
	/**
	 * Telephone field builder
	 * @param string $name
	 * @param array $attrs
	 */
	public function telephone($name, $attrs = array()) {
		$attrs['value']	= (isset($this->model()->{$name})) ? $this->model()->{$name} : null;
		return $this->telephoneField($this->formatName($name), $attrs);
	}
	
	/**
	 * Hidden builder
	 * @param string $name
	 * @param string $value
	 * @param array $attrs
	 */
	public function hidden($name, $value = null, $attrs = array()) {
		$value	= ($value) ? $value : $this->model()->{$name};
		return $this->hiddenFieldTag($this->formatName($name), $value, $attrs);
	}
	
	/**
	 * Password builder
	 * @param string $name
	 * @param array $attrs
	 */
	public function password($name, $attrs = array()) {
		//$attrs['value']	= (isset($this->model()->{$name})) ? $this->model()->{$name} : null;
		return $this->passwordFieldTag($this->formatName($name), $attrs);
	}
	
	/**
	 * Radio button builder
	 * @param string $name
	 * @param string $value
	 * @param array $attrs
	 */
	public function radioButton($name, $value, $attrs = array()) {
		return $this->radioButtonTag($this->formatName($name), $value, $attrs);
	}
	
	/**
	 * Checkbox builder
	 * @param string $name
	 * @param array $attrs
	 */
	public function checkBox($name, $attrs = array()) {
		if (isset($this->model()->{$name})) {
			$attrs['checked']	= 'checked';
		}
		return $this->checkBoxTag($this->formatName($name), $attrs);
	}
	
	/**
	 * Label builder
	 * @param string $name
	 * @param string $label
	 * @param array $attrs
	 */
	public function label($name, $label = null, $attrs = null) {
		return $this->labelTag(
							$this->formatName($name), 
							($label !== null) ? $label : $this->toLabel($name),
							$attrs);
	}
	
	/**
	 * Text field builder
	 * @param string $name
	 * @param array $attrs
	 */
	public function textField($name, $attrs = array()) {
		$attrs['value']	= (isset($this->model()->{$name})) ? $this->model()->{$name} : null;
		return $this->textFieldTag($this->formatName($name), $attrs);
	}
	
	/**
	 * File field builder
	 * @param string $name
	 * @param array $attrs
	 */
	public function fileField($name, $attrs = array()) {
		$attrs['value']	= (isset($this->model()->{$name})) ? $this->model()->{$name} : null;
		return $this->fileFieldTag($this->formatName($name), $attrs);
	}
	
	/**
	 * Select builder from collection
	 * @param string $name
	 * @param array $collection
	 * @param string $key
	 * @param string $value
	 * @param mixed $defaultSelected
	 */
	public function collectionSelect($name, $collection, $key, $value, $defaultSelected = null, $attrs = []) {
		$options = [];
		if (!empty($collection)) {
			foreach ($collection as $record) {
				$options[$record->{$key}] = $record->{$value};
			}
		}
		
		return $this->select($name, $options, $defaultSelected, $attrs);
	}
	
	/**
	 * Select builder
	 * @param string $name
	 * @param array $options
	 * @param mixed $defaultSelected
	 */
	public function select($name, array $options, $defaultSelected = null, $attrs = []) {
		$selected = (isset($this->model()->{$name})) ? $this->model()->{$name} : $defaultSelected;
		return $this->selectTag(
								$this->formatName($name), 
								$this->optionsForSelect($options, $selected), 
								$attrs); 
	}
	
	/**
	 * Magic method for when method is missing
	 * @param string $name
	 * @param array $args
	 * @throws \Exception
	 * @return mixed
	 */
	/*public function __call($name, $args) {
		if (method_exists($this->helper(), $name)) {
			return call_user_func_array(array($this->helper(), $name), $args);
		}
	
		throw new \Exception("No method exists " . get_class($this) . "#$name");
	}*/
	
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
	public function formatName($name) {
		return $this->name() . '.' . $name;
	}
	
	/**
	 * Setter for name
	 * @param string $name
	 * @return \Speedy\View\Helpers\Form
	 */
	protected function setName($name) {
		$this->_name = \Speedy\Utility\Inflector::underscore($name);
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
	
	/**
	 * Setter for model
	 * @param mixed $model
	 * @return \Speedy\View\Helpers\Form
	 */
	protected function setModel($model) {
		$this->_model	= $model;
		return $this;
	}
	
	/**
	 * Getter for model
	 * @return mixed
	 */
	protected function model() {
		return $this->_model;
	}
	
}
?>