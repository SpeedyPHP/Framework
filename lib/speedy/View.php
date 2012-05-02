<?php 
namespace Speedy;

use \Speedy\Loader;
use \Speedy\Config;
use \Speedy\Utility\Inflector;
use \Speedy\Http\Exception as HttpException;
use \Speedy\Singleton;

class View extends Singleton {
	
	/**
	 * Template variables
	 * @var array
	 */
	public $_vars;
	
	public $_yields = array();
	
	protected $_renderers	= array();
	
	
	
	/**
	 * Render the template
	 * @param string $template
	 */
	public function render($file, $options, $ext = 'html') {
		$viewPaths	= Loader::instance()->path('views');
		$renderers	= Config::instance()->renderers();
		
		foreach ($renderers as $type => $renderer) {			
			foreach ($viewPaths as $path) {
				$fullPath	= $path . DS . $file . ".{$type}.{$ext}";
				
				if (!file_exists($fullPath)) {
					continue;
				}
				
				$renderer	= $this->renderer($renderer, $options);
				$renderer
					->setPath($fullPath)
					->setOptions($options)
					->setVars($this->vars())
					->setData($this->getData())
					->render();
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Check if renderer exists in stack
	 * @param string $name
	 * @return boolean
	 */
	private function hasRenderer($name) {
		return isset($this->_renderers[$name]);
	}
	
	/**
	 * Accessor for renderer
	 * @param string $name
	 * @param array $options (optional)
	 * @return \Speedy\View\Base
	 */
	public function renderer($name) {
		if (!$this->hasRenderer($name)) {
			$class	= Loader::instance()->toClass($name);
			$this->setRenderer($name, new $class());
		}
		
		return $this->_renderers[$name];
	}
	
	/**
	 * Setter for renderers
	 * @param string $name
	 * @param \Speedy\View\Base $renderer
	 * @return \Speedy\View
	 */
	protected function setRenderer($name, $renderer) {
		$this->_renderers[$name]	= $renderer;
		return $this;
	}
	
	/**
	 * Getter for vars
	 * @return array
	 */
	public function yield($name) {
		return $this->_yields[$name];
	}
	
	/**
	 * Setter for vars
	 * @param array $vars
	 * @return \Speedy\View
	 */
	public function setYield($name, $value) {
		$this->_yields[$name]	= $value;
		return $this;
	}
	
	/**
	 * Getter for vars
	 * @return array
	 */
	public function vars() {
		return $this->_vars;
	}
	
	/**
	 * Setter for vars
	 * @param array $vars
	 * @return \Speedy\View
	 */
	public function setVars(array $vars) {
		$this->_vars	= $vars;
		return $this;
	}
	
	/**
	 * Find renderer from fileNmae
	 * @param string $fileName
	 * @return string
	 */
	/*public function renderer($fileName) {
		$fileNameArr	= explode('.', $fileName);
		return $fileNameArr[1];
	}*/
	
}

?>