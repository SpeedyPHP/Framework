<?php 
namespace Speedy;

use Speedy\Loader;
use Speedy\Config;
use Speedy\Utility\Inflector;
use Speedy\Http\Exception as HttpException;
use Speedy\View\Exception as ViewException;

class View extends Object {
	
	use \Speedy\Traits\Singleton;
	
	/**
	 * Template variables
	 * @var array
	 */
	public $_vars = [];
	
	public $_options = [];
	
	public $_yields = array();
	
	protected $_renderers	= array();
	
	protected $_response;
	
	protected $_params;
	
	
	
	/**
	 * Render the template
	 * @param string $template
	 */
	public function render($file, $options = [], $vars = [], $ext = 'html') {
		if (isset($options['json'])) return $this->toJson($options['json']);
		if (isset($options['text'])) return $options['json'];
		
		$this
			->setOptions(array_merge($this->options(), $options))
			->setVars(array_merge($this->vars(), $vars));
		
		if (isset($this->_options['layout'])) {
			$tpl = $file;
			$file = 'layouts' . DS . $this->_options['layout'];
			unset($this->_options['layout']);
			$this->setYield('__main__', $this->_render($tpl, $ext));
		}
		
		return $this->_render($file, $ext);
	}
	
	/**
	 * Does the work of rendering
	 * @param string $file
	 * @param string $ext
	 * @throws ViewException
	 * @return string rendered content
	 */
	public function _render($file, $ext) {
		$fullPath	= $this->findFile($file, $ext);
		
		if (!$fullPath) {
			throw new ViewException("No view found for $file using builder => '$ext'");
		}
		
		$renderer 	= $this->builder($fullPath);
		if (!$renderer) 
			throw new ViewException("No view renderer found for $file using builder => '$ext'"); 
		
		$rendererObj= $this->renderer($renderer);
		return $rendererObj->renderTemplate($fullPath, $this->vars());
	}
	
	public function renderToString($file, $options, $vars = null, $ext = 'html') {
		ob_start();
		$this->render($file, $options, $vars, $ext);
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
	}
	
	/**
	 * Figure out the builder
	 * @param string $filePath
	 * @return string
	 */
	public function builder($filePath) {
		if (!file_exists($filePath)) return null;
		
		$fileInfo	= pathinfo($filePath); 
		return Config::instance()->renderer($fileInfo['extension']);
	}
	
	/**
	 * Locate the full file path
	 * @param string $file
	 * @return mixed string fullpath/false on failure
	 */
	public function findFile($file, $ext = 'html') {
		$viewPaths	= Loader::instance()->path('views');
		$renderers	= Config::instance()->renderers();
		
		foreach ($renderers as $type => $renderer) {
			foreach ($viewPaths as $path) {
				$fullPath	= $path . DS . $file . ".{$ext}.{$type}";
				
				if (file_exists($fullPath)) {
					return $fullPath;
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Setter for response
	 * @param \Speedy\Response $response
	 * @return \Speedy\View
	 */
	public function setResponse(\Speedy\Response &$response) {
		$this->_response =& $response;
		return $this;
	}
	
	/**
	 * Getter for response
	 * @return \Speedy\Response
	 */
	public function &response() {
		return $this->_response;
	}
	
	/**
	 * Setter for params
	 * @param array $params
	 */
	public function setParams($params) {
		$this->_params	= $params;
		return $this;
	}
	
	/**
	 * Accessor for params
	 * @return array
	 */
	public function params() {
		return $this->_params;
	}
	
	public function param($name) {
		return $this->__dotAccess($name, $this->_params);
	}
	
	/**
	 * Check is content for yield exists
	 * @param string $name
	 * @return bool
	 */
	public function hasContentFor($name) {
		return isset($this->_yields[$name]);
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
	public function vars($name = null) {
		return ($name) ? $this->_vars[$name]: $this->_vars;
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
	 * Getter for options
	 * @return array
	 */
	public function options() {
		return $this->_options;
	}

	/**
	 * Setter for options
	 * @param array $options
	 * @return object $this
	 */
	public function setOptions($options) {
		$this->_options	= $options;
		return $this;
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
			$obj	= new $class();
			$obj->setParams($this->params())
				->setOptions($this->options())
				->setVars($this->vars())
				->setData($this->data());
			
			$this->setRenderer($name, $obj);
		}
		
		return $this->_renderers[$name];
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
	 * Convert mixed value into json representation
	 * and set headers for reponse
	 * @param mixed $mixed
	 * @return string json representation
	 */
	protected function toJson($mixed) {
		App::instance()->cleanBuffer();
		$this->response()
			->setHeader('Cache-Control', 'no-cache, must-revalidate')
			->setHeader('Expires', date('r'))
			->setHeader('Content-Type', 'application/json');
	
		return json_encode($mixed);
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