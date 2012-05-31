<?php 
namespace Speedy;

use \Speedy\Singleton;
use \Speedy\Cache\Exception as CacheException;

class Cache extends Singleton {

	const DEF	= 'default';
	
	public $path= array();
	
	
	
	public function __construct() {
		$this->_addPath('default', TMP_PATH . DS . 'cache');
	}
	
	/**
	 * Static add a path
	 * @param string $name
	 * @param string $path
	 * @return \Speedy\Cache
	 */
	public static function addPath($name, $path) {
		return self::instance()->_addPath($name, $path);
	}
	
	/**
	 * Static write to cache
	 * @param string $name
	 * @param mixed $data
	 * @param string $setting (optional)
	 * @return \Speedy\Cache
	 */
	public static function write($name, $data, $setting = self::DEF) {
		return self::instance()->_write($name, $data, $setting);
	}
	
	/**
	 * Static read from cache
	 * @param string $name
	 * @param string $setting (optional)
	 * @return mixed
	 */
	public static function read($name, $setting = self::DEF) {
		return self::instance()->_read($name, $setting);
	}
	
	/**
	 * Read from cache
	 * @param string $name
	 * @param string $setting (optional)
	 * @return mixed
	 */
	public function _read($name, $setting = self::DEF) {
		$data	= @file_get_contents($this->fullPath($name, $setting));
		if (!$data) return false;
		
		return unserialize($data);
	}
	
	/**
	 * Write to cache
	 * @param string $name
	 * @param mixed $data
	 * @param string $setting (optional)
	 * @return \Speedy\Cache
	 */
	public function _write($name, $data, $setting = self::DEF) { 
		file_put_contents($this->fullPath($name, $setting), serialize($data));
		return $this;
	}
	
	/**
	 * Getter for path
	 * @param string $path
	 * @return string
	 */
	public function path($path = self::DEF) {
		if (!$this->hasPath($path))
			throw new CacheException("No path found for $path in cache settings");
		
		return $this->path[$path];
	}
	
	/**
	 * Checks if a path exists
	 * @param string $path
	 * @return boolean
	 */
	public function hasPath($path) {
		return isset($this->path[$path]);
	}
	
	/**
	 * Get the full path
	 * @param string $name
	 * @param string $setting
	 * @return string
	 */
	protected function fullPath($name, $setting = self::DEF) {
		return $this->path($setting) . DS . $name;
	}
	
	/**
	 * Add a path
	 * @param string $name
	 * @param string $path
	 * @return \Speedy\Cache
	 */
	protected function _addPath($name, $path) {
		$this->path[$name]	= $path;
		return $this;
	}
	
}
?>