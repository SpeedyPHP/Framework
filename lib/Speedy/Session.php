<?php 
namespace Speedy;

use \Speedy\Singleton;

class Session extends Singleton {
	
	public $flash;
	
	
	
	static function start($name = 'App', $limit = 0, $path = '/', $domain = null, $secure = null) {
		// This implementation borrowed from http://thinkvitamin.com/code/how-to-create-bulletproof-sessions/
		//session_name($name . '_Session');
		
		//$domain = isset($domain) ? $domain : isset($_SERVER['SERVER_NAME']);
		
		//$https	= isset($secure) ? $secure : isset($_SERVER['HTTPS']);
		
		//session_set_cookie_params($limit, $path, $domain, $secure, true);
		session_start();
		
		$self	= self::instance();
		$self->data = &$_SESSION;
	
		if (!$self->has('ip_address')) {
			$self->write('ip_address', $_SERVER['REMOTE_ADDR']);
		}

		if (!$self->has('user_agent')) {
			$self->write('user_agent', $_SERVER['HTTP_USER_AGENT']);
		}
		
		if ($self->has('flash')) {
			$self->flash = $self->read('flash');
			unset($_SESSION['flash']);
		}
	}	
	
	public function preventHijacking() {
		$self	= self::instance();
		// TODO: finish in future
	}
	
	public function write($key, $value) {
		return $this->setData($key, $value);
	}
	
	public function read($key = null) {
		$value = $this->data($key);
		if (isset($value)) return $value;
		elseif (strpos($key, 'flash') !== false) {
			$aKey = explode('.', $key);
			array_shift($aKey);
				
			return $this->__dotAccess(implode('.', $aKey), $this->flash);
		}
		
		return null;
	}
	
	public function erase($key) {
		return $this->unsetData($key);
	}
	
	public function destroy() {
		session_destory();
		return $this;
	}
	
	public function has($name) {
		return ($this->read($name)) ? true : false;
	}
	
}

?>