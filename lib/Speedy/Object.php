<?php
namespace Speedy;

const DEBUG = false;
require_once "Exception.php";

/**
 * Base object for all SpeedyPHP objects
 *
 * @author Zachary Quintana
 * @since 1.0
 * @package Speedy
 */
class Object {
	
	const VS = '.';
	
	/**
	 * Method mixins
	 * @var array of objects
	 */
	protected $_mixins = array();
	
	/**
	 * Mixin objects
	 * @var array of mixin instances
	 */
	protected $_mixinObjs = array();
	
	/**
	 * $var boolean
	 */
	protected $_mixinsLoaded = false;
	
	/**
	 * Holds data for object
	 * @var array 
	 */
	protected $_data = array();
	
	
	
	
	/**
	 * Checks if mixins already loaded
	 * @return boolean
	 */
	protected function _loadedMixins() {
		return $this->_mixinsLoaded;
	}
	
	/**
	 * Checks if mixin is already loaded
	 * @return boolean
	 */
	protected function _hasMixin($mixin) {
		return isset($this->_mixinObjs[$mixin]);
	}
	
	/**
	 * loads mixins
	 * @return $this
	 */
	protected function _loadMixins() {
		if ($this->_loadedMixins()) return $this;
		if ($this->respondsTo('__loadMixins')) {
			$this->_mixins	= array_merge($this->_mixins, (array) $this->__loadMixins());
		}
		
		
		foreach ($this->_mixins as $mixin => $options) {
			if (is_int($mixin)) {
				$mixin = $options;
			}
			
			if (isset($this->_mixinObjs[$mixin])) {
				continue;
			}
			
			if (is_string($mixin) && class_exists($mixin)) {
				$class = $mixin;
			} elseif (is_string($mixin)) {
				import($mixin);
				$class	= \Speedy\Loader::toClass($mixin);
			} 
				
			if (!$class) {
				continue;
			}
			
			$instance	= new $class($this, (is_array($options) ? $options : null));
			if (method_exists($this, "_addPropertiesFromMixin")) {
				call_user_func(array($this, "_addPropertiesFromMixin"), $instance);
			}
			$this->_mixinObjs[$mixin] = $instance;
		}
		
		$this->_mixinsLoaded = true;
		return $this;
	}
	
	/**
	 * Attempts to call a mixin
	 * 
	 */
	protected function _callMixin($name, $args) {
		foreach ($this->_getMixins() as $instance) {
			if ($instance instanceof \Speedy\Object && $instance->respondsTo($name)) {
				return call_user_func_array(array($instance, $name), $args);
			}
		}
		
		throw new Exception("No method exists " . get_class($this) . "#$name");
		
		return null;
	}
	
	protected function __dotIsset($name, &$array) {
		$value	= $this->__dotAccess($name, $array);
		return isset($value);
	}
	
	/**
	 * Get data from array by dot accessor string
	 * @param string $name
	 * @param array $array
	 */
	protected function __dotAccess($name, &$array) {
		if (!$array)return null;
		if ($name === null) return $array;
		if (!empty($array[$name])) {
			return $array[$name];
		}
		
		
		$parts = explode(self::VS, $name);
		$return =& $array;
		
		for ($i = 0; $i < count($parts)-1; $i++) {
			if (isset($return[$parts[$i]]) && is_array($return[$parts[$i]])) {
				$return =& $return[$parts[$i]];
			} else {
				return null;
			}
		}
		
		if (isset($return[$parts[$i]])) {
			return $return[$parts[$i]];
		} else return null;
		
		return $return;
	}
	
	/**
	 * Dot string setter for array
	 * @param string $name
	 * @param mixed $value
	 * @param array $array
	 * @return $this;
	 */
	protected function __dotSetter($name, $value, &$array) {
		$keys 	= explode(self::VS, $name);
		$total 	= count($keys);
		$current=& $array;
		
		for ($i = 0; $i < $total-1; $i++) {
			if (empty($current[$keys[$i]])) {
				$current[$keys[$i]] 	= array();
			}
		
			$current 	=& $current[$keys[$i]];
		}
		
		$current[$keys[$i]] 	= $value;
		return $this;
	}
	
	/**
	 * Sets data
	 * @return $this
	 */
	protected function setData($name, $value = null) {
		if (!$value && is_array($name)) {
			return $this->addData($name);
		} elseif (!$value) {
			return $this->addData($name);
		}
		
		return $this->__dotSetter($name, $value, $this->_data);
	}
	
	protected function addData($data) {
		if (empty($this->_data) && is_array($data)) {
			$this->_data = $data;
		} elseif (is_array($data)) {
			foreach ($data as $key => $value) {
				$this->_data[$key]	= $value;
			}
		} else {
			$this->_data[]	= $data;
		}
		
		return $this;
	}
	
	/**
	 * Checks if a data point is set
	 * @return boolean
	 */
	protected function hasData($name) {
		return ($this->data($name)) ? true : false;
	}
	
	/**
	 * Gets a mixin
	 * @return mixin instance
	 */
	private function _getMixin($mixin) {
		if (empty($this->_mixinObjs) && !$this->_loadedMixins()) {
			$this->_loadMixins();
		}
		
		return ($this->_hasMixin($mixin)) ? $this->_mixinObjs[$mixin] : null;
	}
	
	/**
	 * Gets all mixins
	 * @return array of mixin instances
	 */
	private function _getMixins() {
		if (empty($this->_mixinObjs) && !$this->_loadedMixins()) {
			$this->_loadMixins();
		}
	
		return $this->_mixinObjs;
	}
	
	/**
	 * Gets data
	 * @param name or string path of variable
	 * @return mixed 
	 */
	public function data($name = null) {
		return $this->__dotAccess($name, $this->_data);
	}
	
	public function __set($name, $value) {
		return $this->setData($name, $value);
	}
	
	public function __get($name) {
		return ($this->hasData($name)) ? $this->data($name) : null;
	}
	
	public function __isset($name) {
		return $this->hasData($name);
	}
	
	public function __unset($name) {
		for ($i = 0; $i < $total-1; $i++) {
			if (empty($current[$keys[$i]])) {
				$current[$keys[$i]] 	= array();
			}
		
			$current 	=& $current[$keys[$i]];
		}
		
		unset($current[$keys[$i]]);
		
		return $this;
	}
	
	public function respondsTo($method) {
		return method_exists($this, $method);
	}
	
	/**
	 * Magic methods for magic getters, setters, and methods
	 */
	public function __call($name, $args) {
		if (DEBUG) {
			print_r($name);
			print "\n";
		}
		
		if (!$this->_loadedMixins()) {
			$this->_loadMixins();
		}
		
		preg_match_all('/((?:^|[A-Z])[a-z]+)/', $name, $nameParts);
		$nameParts	= $nameParts[0];
		$verb		= array_shift($nameParts);
		$path		= strtolower(implode(self::VS, $nameParts));
		switch($verb) {
			case "has":
				return $this->hasData($path);
			case "set":
				array_unshift($args, $path);
				return call_user_func_array(array($this, 'setData'), $args);
			case "get":
				return $this->data($path);
			default:
				return $this->_callMixin($name, $args);
		}
	}
}