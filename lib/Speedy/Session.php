<?php 
namespace Speedy;


use Speedy\Singleton;
use Speedy\Config;

class Session {
	
	use Speedy\Traits\Singleton;
	
	public $flash;
	
	private $_manager;
	
	
	
	public function __construct() {
		session_set_save_handler($this->manager());
		$this->manager()->start();
	}	
	
	public function set($key, $value) {
		return $this->manager()->set($key, $value);
	}
	
	public function get($key = null) {
		return $this->manager()->get($key)
	}
	
	public function delete($key) {
		return $this->manager()->delete($key);
	}
	
	public function destroy() {
		return $this->maanger()->destroy();
	}
	
	public function has($name) {
		return ($this->manager()->get($name)) ? true : false;
	}
	
	public static function read($name) {
		return self::instance()->get($name);
	}
	
	public static function write($name, $value) {
		return self::instance()->set($name, $value);
	}
	
	public function manager() {
		if (!$this->_manager) {
			$manager = Config::read('Session.manager'));
			$this->_manager = new $manager;
		}
		
		return $this->_manager;
	}
	
}

?>