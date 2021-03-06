<?php
/**
 * 
 * Class loader for the SpeedyPHP Framework
 * @author Zachary Quintana
 *
 */
namespace Speedy {

  require_once dirname(__FILE__) . DS . "Utility" . DS . "Inflector.php"; 
  require_once dirname(__FILE__) . DS . "Loader" . DS . "Exception.php";
  require_once dirname(__FILE__) . DS . "Object.php";

	const DS 	= DIRECTORY_SEPARATOR;
	
	
	use \Speedy\Utility\Inflector;
	
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
		 * Path aliases
		 * @var array
		 */
		private $_aliases;
		
		/**
		 * 
		 * Holds shared instance
		 * @var Loader
		 */
		private static $_self = null;
		
		
		
		
		/**
		 * Initializer for the singleton
		 * @throws \Speedy\Loader\Exception
		 * @return \Speedy\Loader
		 */
		public static function init() {
			if (self::$_self !== null) {
				throw new Exception('Reattempting to init a singleton');
			}
			
			return new Loader();
		}
		
		/**
		 * 
		 * Returns shared instance of loader
		 * @return Speedy\Loader
		 */
		public static function instance() {
			if (self::$_self == null) {
				self::$_self = self::init();
			}
			
			return self::$_self;
		}
		
		public function __construct() {
			return $this;
		}
		
		/**
		 * Setter for aliases
		 * @param array $aliases
		 * @return \Speedy\Loader
		 */
		public function setAliases(array $aliases) {
			$this->_aliases	= $aliases;
			return $this;
		}
		
		/**
		 * Alias an existing namespace
		 * @param string $alias
		 * @param string $namespace
		 * @return \Speedy\Loader
		 */
		public function addAlias($alias, $namespace) {
			if (!$this->hasNamespace($namespace)) return $this;
			
			if (isset($this->_aliases[$alias])) 
				$this->_aliases[$alias][] = $namespace;	
			else
				$this->_aliases[$alias]	= [$namespace];

			return $this;
		}
		
		/**
		 * Check if an alias exists
		 * @param string $alias
		 * @return boolean
		 */
		private function hasAlias($alias) {
			return !empty($this->_aliases[$alias]);
		}
		
		/**
		 * Return alias if it exists
		 * @param string $alias
		 */
		private function alias($alias) {
			return ($this->hasAlias($alias)) ? $this->_aliases[$alias] : null;
		}
		
		/**
		 * Returns namespaces array
		 * @return array
		 */
		public function namespaces() {
			return $this->_namespaces;	
		}
		
		/**
		 * Registers namespace
		 * @param string namespace
		 * @param string path
		 * @throws \Speedy\Loader\Exception
		 * @return boolean
		 */
		public function registerNamespace($namespace, $path) {
			if ($this->hasNamespace($namespace)) {
				throw new Exception('Namespace already exists');
			}	
			
			$this->_namespaces[$namespace] = $path;
			return true;
		}
		
		/**
		 * Pushes a path to the namespace
		 * @param string $namespace
		 * @param string $path
		 * @return \Speedy\Loader
		 */
		public function pushPathToNamespace($namespace, $path) {
			// Create the namespace if it doesn't exist
			if (!$this->hasNamespace($namespace)) {
				$this->registerNamespace($namespace, array($path));
				return $this;
			}
			
			// Move the namespace into an array if its not already
			if (!is_array($this->_namespaces[$namespace])) {
				$this->_namespaces[$namespace]	= array( $this->path($namespace) );
			}
			
			array_unshift($this->_namespaces[$namespace], $path);
			return $this;
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
		public function path($namespace) {
			if ($this->hasAlias($namespace)) {
				$spaces = $this->alias($namespace);
				$paths = [];

				foreach ($spaces as $space) {
					$paths = array_merge($paths, $this->path($space));
				}

				return $paths;
			}
			
			return ($this->hasNamespace($namespace)) ? $this->_namespaces[$namespace] : null;
		}
		
		/**
		 * Checks if file has already been loaded
		 * @param $path
		 * @return boolean
		 */
		public function loaded($path) {
			return in_array($path, $this->_loaded);
		}
		
		/**
		 * Loads the file
		 * @param $path
		 * @throws \Speedy\Loader\Exception
		 * @return boolean
		 */
		private function load($path) {
			if (!file_exists($path)) {
				throw new \Speedy\Loader\Exception("No file found at $path");
			}
			
			if (!require_once($path)) {
				return false;
			} else {
				$this->_loaded[] = $path;
				return true;
			}
			
			throw new Exception('Unknown error in load');
		}	
		
		/**
		 * Functions like phps require_once but improved
		 * @param pathToClass
		 * @return mixed, false on failure to find and load class
		 */
		public function import($className, $seperator = '\\') {
			// Check if already loaded
			if ($this->loaded($className)) return true;
			
			$path	= $this->toPath($className, $seperator); 
			if (!$path) return false;
			
			if ($this->load($path)) {
				return true;
			} else {
				return false;
			}
			
			return false;
		}
		
		/**
		 * Gets the path for class name
		 * @param string $className
		 * @throws Exception
		 */
		public function toPath($className, $seperator = '\\') {
			if (!strpos($className, $seperator)) return null;

			$aPath = explode($seperator, $className); 
			$firstSpace = Inflector::underscore(array_shift($aPath));
			$secondSpace= Inflector::underscore(array_shift($aPath));
			$ns	= $firstSpace . '.' . $secondSpace;
				
			if (!$this->hasNamespace($ns)) {
				array_unshift($aPath, Inflector::camelize($secondSpace));
				$ns2	= $firstSpace;
			
				if (!$this->hasNamespace($ns2)) {
					return null;
				} else $ns = $ns2;
			}
			if (!$this->hasNamespace($ns)) return null;

			$className = implode($seperator, $aPath);
			$path = str_replace('_', DS, $className);
			$path = str_replace($seperator, DS, $className);
			
			$pathTo = $this->path($ns);
			if (is_array($pathTo)) {
				foreach ($pathTo as $pathAttempt) {
					$fullPath = $pathAttempt . DS . $path . '.php';
					
					if (!file_exists($fullPath)) {
						continue;
					}
					
					return $fullPath;
				}
			} else {
				return $pathTo . DS . $path . '.php';
			}
		}
		
		/**
		 * Converts a class to namespace
		 * @param string $class
		 * @return string
		 */
		public function toNamespace($class) {
			if (!strpos($class, '\\')) return false;
			
			$classArr	= explode('\\', $class);
			foreach ($classArr as &$value) {
				$value	= Inflector::underscore($value);
			}
			$namespace	= array_shift($classArr);
			
			if (!$this->hasNamespace($namespace)) {
				$namespace	= $namespace . '.' . array_shift($classArr);
				if (!$this->hasNamespace($namespace)) return false;
			}
			
			return $namespace . '.' . implode('.', $classArr);
		}
		
		/**
		 * Converts a namespace to a class
		 * @param string $namepace
		 * @return string
		 */
		public function toClass($namespace) {
			$namespaceArr	= explode('.', $namespace);
			foreach ($namespaceArr as &$value) {
				$value	= Inflector::camelize($value);
			}
			
			return implode('\\', $namespaceArr);
		}
		
	}
	
	function autoload($className) {
		if (!strpos($className, '\\')) return false;
	
		//$namespace	= Loader::instance()->toNamespace($className);
		return Loader::instance()->import($className);
	}
	
}

namespace {
	function import($classPath, $vars = null) {
		// Ignore some paths that are already loaded
		$loader = \Speedy\Loader::instance();
		$class = $loader->toClass($classPath);
		
		return $loader->import($class);
	}
	
	spl_autoload_register('Speedy\autoload', false);
}
