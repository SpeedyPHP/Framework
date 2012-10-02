<?php 
namespace Speedy\Cache;


interface CacheInterface {
	
	public function read($key);
	public function write($key, $value);
	public function clear($name);
	public function flush($setting = null);
	
}

?>