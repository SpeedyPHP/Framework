<?php 
namespace Speedy\View\Helpers;

use Speedy\View\Exception as ViewException;
use Speedy\View\Helpers\Exception;
use Speedy\View\Helpers\Form;
use Speedy\Router;
use Speedy\Router\Draw;
use Speedy\Utility\Inflector;
use Speedy\Utility\Links;
use \App;

trait Html {
	
	private $_selfClosing	= array(
		'input',
		'img',
		'link'
	);
	
	private $_jsShorts	= array(
		'jquery' => 'http://code.jquery.com/jquery-1.7.2.min.js'
	);
	
	protected $_linksHelper;
	
	
	
	
	public function __construct() {
		$this->setLinksHelper(\Speedy\Utility\Links::instance());
	}
	
	/**
	 * Generates anchor link
	 * @param string $text
	 * @param string $path
	 * @param array $attributes
	 * @return void
	 */
	public function linkTo($text, $path, $attributes = array()) {
		if (is_string($path)) {
			$attributes['href']	= $path;
		}
		
		$data	= array();
		if (isset($attributes['confirm'])) {
			$data['confirm']= $attributes['confirm'];
			unset($attributes['confirm']); 
		}
		
		if (isset($attributes['method'])) {
			$data['method']	= $attributes['method'];
			unset($attributes['method']);
		}
		
		if (isset($attributes['data']) && is_array($attributes['data'])) {
			$attributes['data']	= array_merge($attributes['data'], $data);
		} else {
			$attributes['data']	= $data;
		}
			
		
		return $this->element('a', $text, $attributes);
	}
	
	/**
	 * Bind a model to a form
	 * @param mixed $model
	 * @param array $attrs
	 * @param closure $closure
	 */
	public function formFor($model, $attrs = null, $closure) {
		if (!$attrs) $attrs	= array();
		
	
		$attrs['method']	= Draw::POST;
		$form	= new Form($model);
		$form->setData($this->data());
		$dynAction = true;

		if (!isset($attrs['action'])) {
			$attrs['action']	= $form->path();
			$dynAction = false;
		}
		
		if (is_array($model)) {
			$model	= array_pop($model);
		}
		
		ob_start();
		if ($model->id && $dynAction) {
			echo $form->hidden('id');
			echo $this->hiddenFieldTag('_method', Draw::PUT);
		}
		$closure($form);
		$content	= ob_get_clean();
		
		return $this->element('form', $content, $attrs);
	}
	
	/**
	 * Form tag helper
	 * @param string $action
	 * @param array $attrs
	 * @param closure $closure
	 * @return void
	 */
	public function formTag($action, $attrs = null, $closure) {
		// TODO: Fix by removing the action parameter
		if (!$attrs) $attrs	= array();
		$attrs['action']	= $action;
		
		ob_start();
		$closure();
		$content = ob_get_clean();
		
		return $this->element('form', $content, $attrs);
	}
	
	/**
	 * Form helper for textarea
	 * @param string $name
	 * @param string $text
	 * @param array $attrs
	 * @throws Exception
	 * @return void
	 */
	public function textAreaTag($name, $text, $attrs = array()) {
		if (!isset($attrs['id'])) {
			$attrs['id'] = $this->toId($name);
		}
		
		if (isset($attrs['size'])) {
			$sizeArr	= explode('x', $attrs['size']);
			
			if (count($sizeArr) != 2) {
				throw new Exception('Unexcepted value for size attribute ' . $attrs['size']);
			}
			
			$attrs['cols']	= $sizeArr[0];
			$attrs['rows']	= $sizeArr[1]; 
		}
		
		$attrs['name']	= $this->toName($name);
		return $this->element('textarea', $text, $attrs);
	}
	
	/**
	 * Email field helper
	 * @param string $name
	 * @param array $attrs
	 * @return void
	 */
	public function emailField($name, $attrs = array()) {
		if (!isset($attrs['id'])) {
			$attrs['id']	= $this->toId($name);
		}
	
		$attrs['name']	= $this->toName($name);
		$attrs['type']	= 'email';
	
		return $this->element('input', null, $attrs);
	}
	
	/**
	 * Url field helper
	 * @param string $name
	 * @param array $attrs
	 * @return void
	 */
	public function urlField($name, $attrs = array()) {
		if (!isset($attrs['id'])) {
			$attrs['id']	= $this->toId($name);
		}
	
		$attrs['name']	= $this->toName($name);
		$attrs['type']	= 'url';
	
		return $this->element('input', null, $attrs);
	}
	
	/**
	 * Telephone field helper
	 * @param string $name
	 * @param array $attrs
	 * @return void
	 */
	public function telephoneField($name, $attrs = array()) {
		if (!isset($attrs['id'])) {
			$attrs['id']	= $this->toId($name);
		}
	
		$attrs['name']	= $this->toName($name);
		$attrs['type']	= 'telephone';
	
		return $this->element('input', null, $attrs);
	}
	
	/**
	 * Search field helper
	 * @param string $name
	 * @param array $attrs
	 * @return void
	 */
	public function searchField($name, $attrs = array()) {
		if (!isset($attrs['id'])) {
			$attrs['id']	= $this->toId($name);
		}
	
		$attrs['name']	= $this->toName($name);
		$attrs['type']	= 'password';
	
		return $this->element('input', null, $attrs);
	}
	
	/**
	 * Hidden field helper
	 * @param string $name
	 * @param string $value
	 * @param array $attrs
	 * @return void
	 */
	public function hiddenFieldTag($name, $value, $attrs = array()) {
		if (!isset($attrs['id'])) {
			$attrs['id']	= $this->toId($name);
		}
	
		$attrs['name']	= $this->toName($name);
		$attrs['type']	= 'hidden';
		$attrs['value']	= $value;
	
		return $this->element('input', null, $attrs);
	}
	
	/**
	 * Password field helper
	 * @param string $name
	 * @param array $attrs
	 * @return void
	 */
	public function passwordFieldTag($name, $attrs = array()) {
		if (!isset($attrs['id'])) {
			$attrs['id']	= $this->toId($name);
		}
	
		$attrs['name']	= $this->toName($name);
		$attrs['type']	= 'password';
	
		return $this->element('input', null, $attrs);
	}
	
	/**
	 * Text field helper
	 * @param string $name
	 * @param array $attrs
	 * @return void
	 */
	public function textFieldTag($name, $attrs = array()) {
		if (!isset($attrs['id'])) {
			$attrs['id']	= $this->toId($name);
		}
		
		$attrs['name']	= $this->toName($name);
		$attrs['type']	= 'text';
		
		return $this->element('input', null, $attrs);
	}
	
	/**
	 * Select field helper
	 * @param string $name
	 * @param string $content
	 * @param array $attrs
	 * @return void
	 */
	public function selectTag($name, $content = '', $attrs = array()) {
		if (!isset($attrs['id'])) {
			$attrs['id']	= $this->toId($name);
		}
		
		$attrs['name']	= $this->toName($name);
		
		return $this->element('select', $content, $attrs);
	}
	
	/**
	 * Options for select helper
	 * @param array $options
	 * @param mixed $selected
	 */
	public function optionsForSelect(array $options, $selected = null) {
		$content = '';
		foreach ($options as $value => $label) {			
			$optContent = '<option value="' . $value . '"';
			if ($selected != null && $selected == $value) {
				$optContent .= ' selected="selected"';
			} 
			$optContent .= '>' . $label . '</option>' . "\n";
			
			$content .= $optContent; 
		}
		
		return $content;
	}
	
	/**
	 * Create options array from collection
	 * @param mixed array/object $collection either array or object that implements \ArrayAccess
	 * @param mixed $key
	 * @param mixed $value
	 */
	public function optionsFromCollectionForSelect($collection, $key, $value) {
		$options = [];
		if (!empty($collection)) {
			foreach ($collection as $record) {
				$options[$record[$key]] = $record[$value];
			}
		}
		
		return $options;
	}
	
	/**
	 * File field helper
	 * @param string $name
	 * @param array $attrs
	 * @return void
	 */
	public function fileFieldTag($name, $attrs = array()) {
		if (!isset($attrs['id'])) {
			$attrs['id']	= $this->toId($name);
		}
		
		$attrs['name']	= $this->toName($name);
		$attrs['type']	= 'file';
		
		return $this->element('input', null, $attrs);
	}
	
	/**
	 * Radio button field helper
	 * @param string $name
	 * @param array $attrs
	 * @return void
	 */
	public function radioButtonTag($name, $value, $attrs = array()) {
		if (!isset($attrs['id'])) {
			$attrs['id']	= $this->toId($name);
		}
	
		$attrs['type']	= 'radio';
		$attrs['value']	= $value;
		$attrs['name']	= $this->toName($name);
	
		return $this->element('input', null, $attrs);
	}
	
	/**
	 * Checkbox field helper
	 * @param string $name
	 * @param array $attrs
	 * @return void
	 */
	public function checkBoxTag($name, $attrs = array()) {
		if (!isset($attrs['id'])) {
			$attrs['id']	= $this->toId($name);
		}
		
		$attrs['type']	= 'checkbox';
		$attrs['value']	= 1;
		$attrs['name']	= $this->toName($name);
		
		return $this->element('input', null, $attrs);
	}
	
	/**
	 * Label tag helper
	 * @param string $input
	 * @param string $label
	 * @return void
	 */
	public function labelTag($input, $label = null, $attrs = null) {
		$attrs = array_merge(
					['for' => $this->toId($input)], 
					(is_array($attrs)) ? $attrs : []);
		return $this->element('label', $label, $attrs);
	}
	
	/**
	 * Submit form helper
	 * 
	 * @param string $label
	 * @return void
	 */
	public function submit($label = 'Submit', $options	= array()) {
		$options	= array_merge(array( 'type' => 'submit', 'value' => $label ), $options);
		return $this->element('input', null, $options);
	} 
	
	/**
	 * Javascript element builder
	 * @param string $file
	 * @return void
	 */
	public function javascript($file, $attributes = array()) {
		$attrs	= array( 'type' => 'text/javascript' );
		
		if ($this->hasJsShort($file)) 
			$attrs['src']	= $this->jsShort($file);
		else $attrs['src']	= $file;
		
		return $this->element('script', '', array_merge($attrs, $attributes), true);
	}
	
	/**
	 * Stylesheet element builder
	 * @param string $file
	 * @param array $attributes (optional)
	 * @return void
	 */
	public function stylesheet($file, $attributes = array()) {
		$attributes['rel']	= 'stylesheet';
		$attributes['href']	= $file;
		
		return $this->element('link', '', $attributes, true);
	}
	
	/**
	 * Generates html element that requires closing tag
	 * @param string $tag
	 * @param string $text
	 * @param array $attributes
	 * @return void
	 */
	public function element($tag, $text = '', $attributes = array(), $nl = false) {
		$html	= "<$tag";
		
		if (count($attributes) > 0) {
			$html .= $this->buildAttributes($attributes);
		}
		
		if ($this->selfClosing($tag)) {
			$html	.= " />";
		} else {
			$html	.= ">$text</$tag>";
		}
		
		return ($nl) ? $html . "\n" : $html;
		//return;
	}
	
	public function toLabel($string) {
		if (strpos($string, '.') === false) return Inflector::titleize($string);
		
		return Inflector::titleize(str_replace('.', '_', $string));
	}
	
	public function __call($name, $args) {
		if ($this->linksHelper()->hasRoutePath($name)) {
			return $this->linksHelper()->__pathToLink($name, $args);
		}
		
		return parent::__call($name, $args);
	}
	
	public function respondsTo($method) {
		if ($this->linksHelper()->hasRoutePath($method)) return true;
		
		return parent::respondsTo($method);
	}
	
	public function linksHelper() {
		if (!$this->_linksHelper) {
			$this->_linksHelper	= Links::instance();
		}
		
		return $this->_linksHelper;
	}
	
	/**
	 * Test the giving element is self closing
	 * @param string $el
	 */
	protected function selfClosing($el) {
		return isset($this->_selfClosing[strtolower($el)]); 
	}
	
	/**
	 * Getter for js url
	 * @param string $name
	 * @return string
	 */
	protected function jsShort($name) {
		return $this->_jsShorts[$name];
	}
	
	/**
	 * Build attributes from array
	 * @param array $attributes
	 * @param string $key (optional)
	 * @return string
	 */
	protected function buildAttributes($attributes, $key = null) {
		$html	= '';
		if (count($attributes) < 1) {
			return $html;
		} 
		
		foreach ($attributes as $name => $value) {
			if ($key) {
				$name	= "{$key}-{$name}";
			}
				
			if (is_array($value)) {
				$html .= $this->buildAttributes($value, $name);
			} elseif(is_bool($value)) {
				if (!$value) {
					continue;
				}

				$html .= " $name";
			} else {
				$html .= ' ' . $name . '="' . $value . '"';
			}
		}
		
		return $html;
	}
	
	/**
	 * Checks if js short available
	 * @param string $name
	 * @return boolean
	 */
	private function hasJsShort($name) {
		return isset($this->_jsShorts[$name]);
	}
	
	/**
	 * Convert a string to name
	 * @param string $string
	 * @return string
	 */
	private function toName($string) {
		if (strpos($string, '.') === false) return $string;
		
		$stringArr	= explode('.', $string);
		$name	= array_shift($stringArr);
		while ($part = array_shift($stringArr)) {
			$name	.= "[$part]";
		}
		
		return $name;
	}
	
	/**
	 * Convert a string to id
	 * @param string $string
	 * @return string
	 */
	private function toId($string) {
		$string	= str_replace(']', '', $string);
		$string = str_replace('[', '_', $string);
		
		return str_replace('.', '_', $string);
	}
	
	/**
	 * Setter for links helper
	 * @param \Speedy\Utility\Links $helper
	 * @return \Speedy\View\Helpers\Html
	 */
	private function setLinksHelper(\Speedy\Utility\Links $helper) {
		$this->_linksHelper	= $helper;
		return $this;
	}
}
?>