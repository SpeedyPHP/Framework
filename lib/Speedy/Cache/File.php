<?php 
namespace Speedy\Cache;


use Exception as CacheException;
use Speedy\Utility\File as FileUtility;

class File implements CacheInterface {

	const PathDefault	= 'default';
	
	public $path= array();
	
	
	
	public function __construct() {
		$this->addPath('default', TMP_PATH . DS . 'cache');
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
			$this->flush($path);	
		}
	}
	
	/**
	 * Clear entire cache for path
	 * @param string $path
	 */
	public function flush($path = null) {
		if ($this->hasPath($path))
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
			
		$path 	= $this->fullPath($name, $setting);
		if (!file_exists($path))
			return false;
		
		$data	= @file_get_contents($path);
		if (!$data) return false;
		
		return unserialize(base64_decode($data));
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
		
		$fullPath = $this->fullPath($name, $setting);
		$parts = pathinfo($fullPath);

		if (!file_exists($parts['dirname']))
			FileUtility::mkdir_p($parts['dirname']);

		file_put_contents($fullPath, base64_encode(serialize($data)));
		chmod($fullPath, 0775);
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
			$path = self::PathDefault;
		
		// if the path doesn't exist attempt to create it
		if (!file_exists($this->path[$path])) {
			FileUtility::mkdir_p($this->path[$path], 0755);
		}
		
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
	protected function fullPath($name, $setting = self::PathDefault) {
		return $this->path($setting) . DS . $name;
	}
	
}
?>
