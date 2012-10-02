<?php 
namespace ActiveRecord\Cache;


interface CacheInterface {
	
	public function flush();
	public function read($key);
	public function write($key, $value);
	public function clear($name);
	public function clearAll();
	
}

?>