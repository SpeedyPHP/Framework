<?php 
namespace Speedy;


use Speedy\Config;

class Cache {

	use \Speedy\Traits\Singleton;
	
	private $_manager;
	
	
	public function __construct() {
		
	}
	
	public function manager() {
		if (!$this->_manager) {
			$class	= Config::read('Config.manager');
			$this->_manager = new $class();
		}
		
		return $this->_manager;
	}
	
	/**
	 * Static write to cache
	 * @param string $name
	 * @param mixed $data
	 * @param string $setting (optional)
	 * @return \Speedy\Cache
	 */
	public static function write($name, $data, $setting = null) {
		return self::instance()->manager()->write($name, $data, $setting);
	}
	
	/**
	 * Static read from cache
	 * @param string $name
	 * @param string $setting (optional)
	 * @return mixed
	 */
	public static function read($name, $setting = null) {
		return self::instance()->manager()->read($name, $setting);
	}
	
	/**
	 * Clear a cache
	 * @param string $name
	 * @param string $setting
	 */
	public static function clear($name = null, $path = null) {
		return self::instance()->manager()->clear($name, $path);
	}
	
	/**
	 * Clear entire cache for path
	 * @param string $path
	 */
	public function flush($path = null) {
		return self::instance()->manager()->flush($path = null);
	} 
	
}
?>
