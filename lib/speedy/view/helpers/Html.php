<?php 
namespace Speedy\View\Helpers;

use \Speedy\View\Exception as ViewException;
use \Speedy\View\Helpers\Exception;
use \Speedy\View\Helpers\Base;
use \Speedy\View\Helpers\Form;
use \Speedy\Router;
use \Speedy\Router\Draw;
use \Speedy\Utility\Inflector;
use \Speedy\Utility\Links;
use \App;

class Html extends Base {
	
	private $_selfClosing	= array(
		'input',
		'img'
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
		
		if (empty($model->id))
			$attrs['method']	= Draw::POST;
		else 
			$attrs['method']	= Draw::PUT;
		
		$form	= new Form($model, $this);
		$attrs['action']	= $form->path();
		
		ob_start();
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
	public function labelTag($input, $label = null) {
		return $this->element('label', $label, array( 'for' => $this->toId($input) ));
	}
	
	/**
	 * Submit form helper
	 * 
	 * @param string $label
	 * @return void
	 */
	public function submit($label = 'Submit') {
		return $this->element('input', null, array( 'type' => 'submit', 'value' => $label ));
	} 
	
	/**
	 * Generates html element that requires closing tag
	 * @param string $tag
	 * @param string $text
	 * @param array $attributes
	 * @return void
	 */
	public function element($tag, $text = '', $attributes = array()) {
		$html	= "<$tag";
		
		if (count($attributes) > 0) {
			foreach ($attributes as $name => $value) {
				$html .= ' ' . $name . '="' . $value . '"';
			}
		}
		
		if ($this->selfClosing($tag)) {
			$html	.= " />";
		} else {
			$html	.= ">\n$text\n</$tag>";
		}
		
		echo $html;
		return;
	}
	
	public function toLabel($string) {
		if (strpos($string, '.') === false) return Inflector::titleize($string);
		
		return Inflector::titleize(str_replace('.', '_', $string));
	}
	
	public function __call($name, $args) {
		if ($this->linksHelper()->hasRoutePath($name)) {
			return $this->linksHelper()->__pathToLink($name, $args);
		}
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
		if (strpos($string, '.') === false) return $string;
		
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