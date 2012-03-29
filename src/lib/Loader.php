<?php
/**
 * 
 * Class loader for the Vectorized PHP Framework
 * @author Zachary Quintana
 *
 */
namespace Vzed;

const DS 	= DIRECTORY_SEPARATOR;

ini_set('include_path', getenv('VZED_PATH') . PATH_SEPARATOR . getenv('include_path'));
require_once "Object.php";

class Loader extends Object {
	
	/**
	 * 
	 * Map of namespace to path
	 * @var array
	 */
	private $_namespaces = array();
	
	/**
	 * Loaded files
	 * @var array
	 */
	private $_loaded = array();
	
	/**
	 * 
	 * Holds shared instance
	 * @var Loader
	 */
	private static $_self = null;
	
	
	public static function init() {
		if (self::$_self !== null) {
			// TODO: throw exception
		}
		
		return new Loader();
	}
	
	/**
	 * 
	 * Returns shared instance of loader
	 * @return Vzed\Loader
	 */
	public static function getInstance() {
		if (self::$_self == null) {
			self::$_self = self::init();
		}
		
		return self::$_self;
	}
	
	public function __construct() {
		$this->registerNamespace('vzed', $_ENV['VZED_PATH']);
		
		return $this;
	}
	
	/**
	 * Returns namespaces array
	 * @return array
	 */
	public function getNamespaces() {
		return $this->_namespaces;	
	}
	
	/**
	 * Registers namespace
	 * @param string namespace
	 * @param string path
	 * @return boolean
	 */
	public function registerNamespace($namespace, $path) {
		if ($this->hasNamespace($namespace)) {
			// TODO: throw exception
			return false;
		}	
		
		$this->_namespaces[$namespace] = $path;
		return true;
	}
	
	/**
	 * Checks if namespace exists
	 * @param $namespace
	 * @return boolead
	 */
	public function hasNamespace($namespace) {
		return !empty($this->_namespaces[$namespace]);
	}
	
	/**
	 * Gets absolute path to namespace
	 * @param $namespace
	 * @return string of path
	 */
	public function getPath($namespace) {
		return ($this->hasNamespace($namespace)) ? $this->_namespaces[$namespace] : null;
	}
	
	/**
	 * Checks if file has already been loaded
	 * @param $path
	 * @return boolean
	 */
	public function loaded($path) {
		return isset($this->_loaded[$path]);
	}
	
	/**
	 * Loads the file
	 * @param $path
	 * @return boolean
	 */
	private function load($path) {
		if (!require_once($path)) {
			return false;
		} else {
			$this->_loaded[$path] = true;
			return true;
		}
		
		// TODO: Raise exception
		return false;
	}	
	
	/**
	 * Functions like phps require_once but improved
	 * @param pathToClass
	 * @return mixed, false on failure to find and load class
	 */
	public function import($namespace) {
		// Check if already loaded
		if ($this->loaded($namespace)) return true;
		
		// Explode the $namespace and to build path
		$aPath 		= explode('.', $namespace);
		$namespace 	= array_shift($aPath);
		
		if (!$this->hasNamespace($namespace)) {
			$namespace	= $namespace . '.' . array_shift($aPath);
			if (!$this->hasNamespace($namespace)) return false;
		}
		
		$aClass	= array();
		foreach ($aPath as $val) {
			$aClass[] = ucfirst($val);
		}
		
		// Attempt to find and load the file return result
		$pathTo = $this->getPath($namespace);
		$path	= $pathTo . DS . implode(DS, $aPath) . '.php';
		$path = $pathTo . DS . implode(DS, $aClass) . '.php';
		if (file_exists($path)) {
			if ($this->load($path)) {
				return '\\' . implode('\\', $aClass);
			} else {
				return false;
			}
		}
		
		if (file_exists($path)) {
			if ($this->load($path)) {
				return '\\' . implode('\\', $aClass);
			} else {
				return false;
			}
		} 
		
		return false;
	}
	
}

function import($classPath) {
	$loader = Loader::getInstance();
	
	return $loader->import($classPath);
}

//spl_auto_register(__NAMESPACE__ . '\import');