<?php 
namespace Speedy;

use \Speedy\Singleton;

class Session extends Singleton {
	
	
	static function start($name = 'App', $limit = 0, $path = '/', $domain = null, $secure = null) {
		// This implementation borrowed from http://thinkvitamin.com/code/how-to-create-bulletproof-sessions/
		//session_name($name . '_Session');
		
		//$domain = isset($domain) ? $domain : isset($_SERVER['SERVER_NAME']);
		
		//$https	= isset($secure) ? $secure : isset($_SERVER['HTTPS']);
		
		//session_set_cookie_params($limit, $path, $domain, $secure, true);
		session_start();
		
		$self	= self::instance();
	
		if (!$self->has('ip_address')) {
			$self->write('ip_address', $_SERVER['REMOTE_ADDR']);
		}

		if (!$self->has('user_agent')) {
			$self->write('user_agent', $_SERVER['HTTP_USER_AGENT']);
		}
	}	
	
	public function preventHijacking() {
		$self	= self::instance();
		// TODO: finish in future
	}
	
	public function write($key, $value) {
		return $this->__dotSetter($key, $value, $_SESSION);
	}
	
	public function read($key = null) {
		return $this->__dotAccess($key, $_SESSION);
	}
	
	public function destroy() {
		session_destory();
		return $this;
	}
	
	public function __destruct() {
		// $_SESSION	= self::instance()->data();
	}
	
	public function has($name) {
		return ($this->read($name)) ? true : false;
	}
	
	protected function addData(&$data) {
		if (empty($this->_data) && is_array($data)) {
			$this->_data =& $data;
		} elseif (is_array($data)) {
			foreach ($data as $key => $value) {
				$this->_data[$key]	= $value;
			}
		} else {
			$this->_data[]	= $data;
		}
	
		return $this;
	}
	
}

?>