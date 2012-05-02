<?php 
namespace Speedy\View\Helpers;

use \Speedy\View\Exception as ViewException;
use \Speedy\View\Helpers\Exception;
use \Speedy\View\Helpers\Base;
use \Speedy\View\Helpers\Form;
use \Speedy\Router;
use \Speedy\Router\Draw;
use \Speedy\Utility\Inflector;
use \App;

class Html extends Base {
	
	private $_selfClosing	= array(
		'input',
		'img'
	);
	
	private $_routePaths	= array();
	
	
	
	public function __construct() {
		$this->loadRoutes();
	}
	
	/**
	 * Generates anchor link
	 * @param string $text
	 * @param string $path
	 * @param array $attributes
	 */
	public function linkTo($text, $path, $attributes = array()) {
		$short_links	= App::instance()->config()->get('short_links');
		if (!$short_links && strpos($path, '/') === 0) {
			$path	= substr($path, 1);
		}
		
		if (is_string($path)) {
			if (!$short_links) {
				$path	= "/index.php?url=$path";
			}
			
			$attributes['href']	= $path;
		}
			
		
		return $this->element('a', $text, $attributes);
	}
	
	public function formFor($model, $attrs = null, $closure) {
		if (!$attrs) $attrs	= array();
		
		if (!isset($model->id))
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
	 */
	public function labelTag($input, $label = null) {
		return $this->element('label', $label, array( 'for' => $this->toId($input) ));
	}
	
	/**
	 * Generates html element that requires closing tag
	 * @param string $tag
	 * @param string $text
	 * @param array $attributes
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
	}
	
	/**
	 * Test the giving element is self closing
	 * @param string $el
	 */
	protected function selfClosing($el) {
		return isset($this->_selfClosing[strtolower($el)]); 
	}
	
	/**
	 * Loads all the route paths
	 */
	private function loadRoutes() {
		$routes	= Router::instance()->routes();
		
		foreach ($routes as $route) {
			$name	= $route->name();
			if (!$name) {
				continue;
			}
			
			$this->pushRoutePath($name, array(
				'format'	=> $route->format(),
				'tokens'	=> $route->token()
			));
		}
	}
	
	/**
	 * Push named helper into path
	 * @param string $name
	 * @return \Speedy\View\Helpers\Html
	 */
	private function pushRoutePath($name, $format) {
		if (isset($this->_routePaths[$name])) return $this;
		$this->_routePaths[$name]	= $format;
		
		return $this;
	}
	
	/**
	 * Checks if the path is available
	 * @param string $name
	 * @return boolean
	 */
	public function hasRoutePath($name) {
		return isset($this->_routePaths[$name]);
	}
	
	/**
	 * Getter for route path
	 * @param string $name
	 * @return mixed 
	 */
	public function routePath($name) {
		return ($this->hasRoutePath($name)) ? $this->_routePaths[$name] : null;
	}
	
	public function __call($name, $args) {
		if ($this->hasRoutePath($name)) {
			return $this->__pathToLink($name, $args);
		}
	}
	
	public function respondsTo($name) {
		if ($this->hasRoutePath($name)) return true;
		
		return method_exists($this, $name);
	}
	
	private function __pathToLink($name, $args) {
		$path	= $this->routePath($name);
		extract($path);
			
		if (count($args) < count($tokens)) {
			throw new Exception('No route matches ' . $format);
		}
			
		foreach ($tokens as $token) {
			$format	= str_replace(":{$token}", array_shift($args), $format);
		}
			
		if (!empty($args)) {
			while($param = array_shift($args)) {
				$format .= "/{$param}";
			}		
		}
		
		return $format;
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
	
	private function toLabel($string) {
		if (strpos($string, '.') === false) return Inflector::titleize($string);
		
		return Inflector::titleize(str_replace('.', '_', $string));
	} 
}
?>