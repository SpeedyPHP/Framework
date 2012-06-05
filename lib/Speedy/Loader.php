<?php
/**
 * 
 * Class loader for the SpeedyPHP Framework
 * @author Zachary Quintana
 *
 */
namespace Speedy {

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
			if ($this->hasNamespace($namespace)) return $this;
			
			$this->_aliases[$alias]	= $namespace;
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
		public function getNamespaces() {
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
			if (!is_array($this->_namespace[$namespace])) {
				$this->_namespace[$namespace]	= array( $this->path($namespace) );
			}
			
			$this->_namespace[$namespace][]	= $path;
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
			if ($this->hasAlias($namespace)) return $this->path($this->alias($namespace));
			
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
		public function import($namespace) {
			// Check if already loaded
			if ($this->loaded($namespace)) return true;
			
			$path	= $this->toPath($namespace); 
			if ($this->load($path)) {
				return true;
			} else {
				return false;
			}
			
			return false;
		}
		
		/**
		 * Gets the path for a namespace
		 * @param string $namespace
		 * @throws Exception
		 */
		public function toPath($namespace) {
			// Explode the $namespace and to build path
			$aPath 		= explode('.', $namespace); 
			$firstSpace = array_shift($aPath);
			$secondSpace= array_shift($aPath);
			$relNamespace	= $firstSpace . '.' . $secondSpace; 
			
			if (!$this->hasNamespace($relNamespace)) {
				array_unshift($aPath, $secondSpace);
				$relNamespace2	= $firstSpace; 
				
				if (!$this->hasNamespace($relNamespace2)) {
					throw new Exception('No namespace for ' . $relNamespace);
				} else $relNamespace = $relNamespace2;
			}
			
			$aClass	= array();
			$total	= count($aPath) - 1;
			foreach ($aPath as $index => $val) {
				if ($index < $total) {
					$aClass[]	= Inflector::underscore($val);
				} else {
					$aClass[]	= Inflector::camelize($val);
				}
			}
			// Attempt to find and load the file return result
			$pathTo = $this->path($relNamespace);
			
			// if the $pathTo is an array then loop all potential
			// paths to find the correct one. Otherwise check
			// if the file exists and return it
			if (is_array($pathTo)) {
				foreach ($pathTo as $path) {
					$fullPath	= $path . DS . implode(DS, $aClass) . '.php';
					
					if (!file_exists($fullPath)) {
						continue;
					}
					
					return $fullPath;
				} 
				
				throw new Exception("No path found for $namespace for path " . implode(':', $pathTo));
			} else {
				$fullPath	= $pathTo . DS . implode(DS, $aClass) . '.php';
	
				if (!file_exists($fullPath)) {
					throw new Exception("No path found for $namespace for path " . $pathTo);
				}
			}
			
			return $fullPath;
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
			
			return '\\' . implode('\\', $namespaceArr);
		}
		
	}
	
	function autoload($className) {
		if (!strpos($className, '\\')) return false;
	
		$namespace	= Loader::instance()->toNamespace($className);
		return import($namespace);
	}
	
}

namespace {
	function import($classPath, $vars = null) {
		// Ignore some paths that are already loaded
		if ($classPath == 'speedy.object') return;
		if ($classPath == 'speedy.utility.inflector') return;
		if ($classPath == 'speedy.loader.exception') return;
		$loader = \Speedy\Loader::instance();
		
		return $loader->import($classPath);
	}
	
	spl_autoload_register('Speedy\autoload', false);
	
	function debug($obj) {
		echo "<pre>";
		if (is_array($obj)) {
			print_r($obj);
		} else {
			var_dump($obj);
		}
		echo "</pre>";
	}
	
	function rglob($pattern='*', $flags = 0, $path='')
	{
		$paths	= glob($path.'*', GLOB_MARK|GLOB_ONLYDIR|GLOB_NOSORT|GLOB_BRACE);
		$files	= glob($path.$pattern, $flags);
		foreach ($paths as $path) {
			$files	= array_merge($files,rglob($pattern, $flags, $path));
		}
		return $files;
	}
}