<?php 
namespace Speedy\Cache;


use Exception as CacheException;

class Cache implements CacheInterface {

	const PathDefault	= 'default';
	
	public $path= array();
	
	
	
	public function __construct() {
		$this->_addPath('default', TMP_PATH . DS . 'cache');
	}
	
	/**
	 * Clear a cache
	 * @param string $name
	 * @param string $setting
	 */
	public function clear($name = null, $path = null) {
		if ($path == null) $path = self::PathDefault;
		
		if ($name) {
			$filepath = $this->path($path) . DS . $name;
			if (file_exists($filepath)) {
				@unlink($filepath);
			}
			
			return;
		} else {
			$this->clearAll($path);	
		}
	}
	
	/**
	 * Clear entire cache for path
	 * @param string $path
	 */
	public function clearAll($path = null) {
		$path = $this->path($path);
		foreach (glob($path . DS . "*") as $filename) {
			@unlink($filename);
		}
	} 
	
	/**
	 * Read from cache
	 * @param string $name
	 * @param string $setting (optional)
	 * @return mixed
	 */
	public function read($name, $setting = null) {
		if ($setting == null) $setting = self::PathDefault;
			
		$data	= @file_get_contents($this->fullPath($name, $setting));
		if (!$data) return false;
		
		return unserialize($data);
	}
	
	/**
	 * Write to cache
	 * @param string $name
	 * @param mixed $data
	 * @param string $setting (optional)
	 * @return object $this
	 */
	public function write($name, $data, $setting = null) {
		if ($setting == null) $setting = self::PathDefault;
		
		file_put_contents($this->fullPath($name, $setting), serialize($data));
		return $this;
	}
	
	/**
	 * Getter for path
	 * @param string $path
	 * @return string
	 */
	public function path($path = null) {
		if ($path == null) $path = self::PathDefault;
		
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
	 * Add a path
	 * @param string $name
	 * @param string $path
	 * @return \Speedy\Cache
	 */
	public function addPath($name, $path) {
		$this->path[$name]	= $path;
		return $this;
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
	
}
?>