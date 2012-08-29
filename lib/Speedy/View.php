<?php 
namespace Speedy;

use \Speedy\Loader;
use \Speedy\Config;
use \Speedy\Utility\Inflector;
use \Speedy\Http\Exception as HttpException;
use \Speedy\View\Exception as ViewException;
use \Speedy\Singleton;

class View extends Singleton {
	
	/**
	 * Template variables
	 * @var array
	 */
	public $_vars;
	
	public $_yields = array();
	
	protected $_renderers	= array();
	
	protected $_response;
	
	protected $_params;
	
	
	
	/**
	 * Render the template
	 * @param string $template
	 */
	public function render($file, $options, $ext = 'html') {
		if (isset($options['json'])) return $this->toJson($options['json']);
		
		// $viewPaths	= Loader::instance()->path('views');
		// $renderers	= Config::instance()->renderers();
		
		/*foreach ($renderers as $type => $renderer) {			
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
					->setData($this->data())
					->render();
				return true;
			}
		}*/
		$fullPath	= $this->findFile($file, $ext);  
		if (!$fullPath) {
			throw new ViewException("No view found for $file using builder => '$ext'");
		}
		
		$renderer 	= $this->builder($fullPath); 
		if (!$renderer) return false; // TODO: Throw exception
		
		$rendererObj= $this->renderer($renderer, $options);
		$rendererObj
			->setPath($fullPath)
			->setOptions($options)
			->setVars($this->vars())
			->setData($this->data())
			->render(); 
		
		return true;
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
		if (strpos($file, '/') === false) {
			
		}
		
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
		return $this->response;
	}
	
	/**
	 * Setter for params
	 * @param array $params
	 */
	public function setParams($params) {
		$this->params	= $params;
		return $this;
	}
	
	/**
	 * Accessor for params
	 * @return array
	 */
	public function params() {
		return $this->params;
	}
	
	/**
	 * Check is content for yield exists
	 * @param string $name
	 * @return bool
	 */
	public function hasContentFor($name) {
		return isset($this->_yield[$name]);
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
	 * Accessor for renderer
	 * @param string $name
	 * @param array $options (optional)
	 * @return \Speedy\View\Base
	 */
	public function renderer($name, array $options = []) {
		if (!$this->hasRenderer($name)) {
			$class	= Loader::instance()->toClass($name);
			$obj	= new $class();
			$obj->setParams($this->params())
				->setOptions($options);
			
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
		$this->response()
			->setHeader('Cache-Control', 'no-cache, must-revalidate')
			->setHeader('Expires', date('r'))
			->setHeader('Content-Type', 'application/json');
	
		echo json_encode($mixed);
		return true;
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