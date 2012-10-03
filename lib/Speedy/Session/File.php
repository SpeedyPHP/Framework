<?php 
namespace Speedy\Session;



use Speedy\Config;
use Speedy\Request;

class File extends Base {
	
	use \Speedy\Traits\ArrayAccess;
	
	/**
	 * True if the Session is still valid
	 *
	 * @var boolean
	 */
	public $valid = false;
	
	/**
	 * Error messages for this session
	 *
	 * @var array
	 */
	public $error = false;
	
	/**
	 * User agent string
	 *
	 * @var string
	 */
	protected $_userAgent = '';
	
	/**
	 * Path to where the session is active.
	 *
	 * @var string
	 */
	public $path = '/';
	
	/**
	 * Error number of last occurred error
	 *
	 * @var integer
	 */
	public $lastError = null;
	
	/**
	 * Start time for this session.
	 *
	 * @var integer
	 */
	public $time = false;
	
	/**
	 * Cookie lifetime
	 *
	 * @var integer
	 */
	public static $cookieLifeTime;
	
	/**
	 * Time when this session becomes invalid.
	 *
	 * @var integer
	 */
	public $sessionTime = false;
	
	/**
	 * Current Session id
	 *
	 * @var string
	 */
	public $id = null;
	
	/**
	 * Hostname
	 *
	 * @var string
	 */
	public $host = null;
	
	/**
	 * Session timeout multiplier factor
	 *
	 * @var integer
	 */
	public static $timeout = null;
	
	/**
	 * Number of requests that can occur during a session time without the session being renewed.
	 * This feature is only used when config value `Session.autoRegenerate` is set to true.
	 *
	 * @var integer
	 * @see 
	 */
	public static $requestCountdown = 10;
	
	/**
	 * Current flash
	 * @var array
	 */
	public $flash;
	
	
	
	/**
	 * Pseudo constructor.
	 *
	 * @param string $base The base path for the Session
	 * @return void
	 */
	public function __construct($base = null) {
		$this->time = time();
		$this->configure();
		
		$checkAgent = Config::read('Session.checkAgent');
		if (($checkAgent === true || $checkAgent === null) && Request::get('HTTP_USER_AGENT') != null) {
			$this->_userAgent = md5(Request::get('HTTP_USER_AGENT') . Config::read('Security.salt'));
		}
		$this->setPath($base)
			->setHost(Request::get('HTTP_HOST'));
		
		if ($this->start() && $this->has('flash')) {
			$this->flash = ['flash' => $this->get('flash')];
			$this->delete('flash');
		}
		//register_shutdown_function('session_write_close');
	}
	
	public function configure() {
		$config = array_merge($this->defaults(), Config::read('Session'));
		
		$this->sessionTime	= $this->time + ($config['timeout'] * 60);
	}
	
	/**
	 * Setup the Path variable
	 *
	 * @param string $base base path
	 * @return void
	 */
	protected function setPath($base = null) {
		if (empty($base)) {
			$this->path = '/';
			return $this;
		}
		if (strpos($base, 'index.php') !== false) {
			$base = str_replace('index.php', '', $base);
		}
		if (strpos($base, '?') !== false) {
			$base = str_replace('?', '', $base);
		}
		$this->path = $base;
		return $this;
	}
	
	/**
	 * Set the host name
	 *
	 * @param string $host Hostname
	 * @return void
	 */
	protected function setHost($host) {
		$this->host = $host;
		if (strpos($this->host, ':') !== false) {
			$this->host = substr($this->host, 0, strpos($this->host, ':'));
		}
		return $this;
	}
	
	protected function setUserAgent($agent) {
		$this->_userAgent = $agent;
		return $this;
	}
	
	/**
	 * Starts the Session.
	 *
	 * @return boolean True if session was started
	 */
	public function start() {
		if ($this->started()) {
			return true;
		}
		
		$id = $this->id();
		session_write_close();
		
		if (headers_sent()) {
			if (empty($_SESSION)) {
				$_SESSION = array();
			}
		} else {
			// For IE<=8
			session_cache_limiter("must-revalidate");
			session_start();
		}
	
		if (!$id && $this->started()) {
			$this->checkValid();
		}
	
		$this->error = false;
		return $this->started();
	}
	
	/**
	 * Determine if Session has been started.
	 *
	 * @return boolean True if session has been started.
	 */
	public function started() {
		return isset($_SESSION) && session_id();
	}
	
	/**
	 * Returns true if given variable is set in session.
	 *
	 * @param string $name Variable name to check for
	 * @return boolean True if variable is there
	 */
	public function check($name) {
		if (!$this->started()) return false;
		
		return $this->__dotIsset($name, $_SESSION);
	}
	
	/**
	 * Returns the Session id
	 *
	 * @param string $id
	 * @return string Session id
	 */
	public function id($id = null) {
		if ($id) {
			$this->id = $id;
			session_id($this->id);
		}
		if ($this->started()) {
			return session_id();
		}
		return $this->id;
	}
	
	/**
	 * Removes a variable from session.
	 *
	 * @param string $name Session variable to remove
	 * @return boolean Success
	 */
	public function delete($name) {
		if ($this->check($name)) {
			$this->__dotUnset($name, $_SESSION);
			return ($this->check($name) == false);
		}
		$this->setError(2, sprintf("%s doesn't exist", $name));
		return false;
	}
	
	/**
	 * Used to write new data to _SESSION, since PHP doesn't like us setting the _SESSION var itself
	 *
	 * @param array $old Set of old variables => values
	 * @param array $new New set of variable => value
	 * @return void
	 *
	protected static function _overwrite(&$old, $new) {
		if (!empty($old)) {
			foreach ($old as $key => $var) {
				if (!isset($new[$key])) {
					unset($old[$key]);
				}
			}
		}
		foreach ($new as $key => $var) {
			$old[$key] = $var;
		}
	}*/
	
	/**
	 * Return error description for given error number.
	 *
	 * @param integer $errorNumber Error to set
	 * @return string Error as string
	 */
	protected function error($errorNumber) {
		if (!is_array($this->error) || !array_key_exists($errorNumber, $this->error)) {
			return false;
		} else {
			return $this->error[$errorNumber];
		}
	}
	
	/**
	 * Returns last occurred error as a string, if any.
	 *
	 * @return mixed Error description as a string, or false.
	 */
	public function lastError() {
		if ($this->lastError) {
			return $this->error($this->lastError);
		}
		return false;
	}
	
	/**
	 * Returns true if session is valid.
	 *
	 * @return boolean Success
	 */
	public function valid() {
		if ($this->get('Config')) {
			if ($this->validAgentAndTime() && $this->error === false) {
				$this->valid = true;
			} else {
				$this->valid = false;
				$this->setError(1, 'Session Highjacking Attempted !!!');
			}
		}
		return $this->valid;
	}
	
	/**
	 * Tests that the user agent is valid and that the session hasn't 'timed out'.
	 * Since timeouts are implemented in CakeSession it checks the current self::$time
	 * against the time the session is set to expire.  The User agent is only checked
	 * if Session.checkAgent == true.
	 *
	 * @return boolean
	 */
	protected function validAgentAndTime() {
		$config = $this->get('Config');
		$validAgent = (
				Config::read('Session.checkAgent') === false ||
				$this->_userAgent == $config['userAgent']
		);
		return ($validAgent && $this->time <= $config['time']);
	}
	
	/**
	 * Get / Set the userAgent
	 *
	 * @param string $userAgent Set the userAgent
	 * @return void
	 */
	public function userAgent() {
		return $this->_userAgent;
	}
	
	/**
	 * Returns given session variable, or all of them, if no parameters given.
	 *
	 * @param string|array $name The name of the session variable (or a path as sent to Set.extract)
	 * @return mixed The value of the session variable
	 */
	public function get($name = null) {
		if (!$this->started()) return false;
		if (is_null($name)) return $_SESSION;
		
		
		$result = $this->__dotAccess($name, $_SESSION);
		if (isset($result)) 
			return $result;
		
		$result = $this->__dotAccess($name, $this->flash);
		if (isset($result)) 
			return $result;
		
		$this->setError(2, "$name doesn't exist");
		return null;
	}
	
	/**
	 * Returns all session variables.
	 *
	 * @return mixed Full $_SESSION array, or false on error.
	 *
	protected static function _returnSessionVars() {
		if (!empty($_SESSION)) {
			return $_SESSION;
		}
		self::_setError(2, 'No Session vars set');
		return false;
	}*/
	
	/**
	 * Writes value to given session variable name.
	 *
	 * @param string|array $name Name of variable
	 * @param string $value Value to write
	 * @return boolean True if the write was successful, false if the write failed
	 */
	public function set($name, $value = null) {
		if (!$this->started() && !$this->start()) return false;
		if (empty($name)) return false;
		
		/*$write = $name;
		if (!is_array($name)) {
			$write = array($name => $value);
		}
		foreach ($write as $key => $val) {
			self::_overwrite($_SESSION, Hash::insert($_SESSION, $key, $val));
			if (Hash::get($_SESSION, $key) !== $val) {
				return false;
			}
		}*/
		return $this->__dotSetter($name, $value, $_SESSION);
		// return true;
	}
	
	public function has($key) {
		return ($this->__dotIsset($key, $_SESSION) || $this->__dotIsset($key, $this->flash));
	}
	
	/**
	 * Clears the session, the session id, and renew's the session.
	 *
	 * @return void
	 */
	public function clear() {
		$_SESSION = null;
		$this->id = null;
		$this->start();
		$this->renew();
	}
	
	/**
	 * Helper method to initialize a session, based on Cake core settings.
	 *
	 * Sessions can be configured with a few shortcut names as well as have any number of ini settings declared.
	 *
	 * @return void
	 * @throws CakeSessionException Throws exceptions when ini_set() fails.
	 *
	protected static function _configureSession() {
		$sessionConfig = Configure::read('Session');
	
		if (isset($sessionConfig['defaults'])) {
			$defaults = self::_defaultConfig($sessionConfig['defaults']);
			if ($defaults) {
				$sessionConfig = Hash::merge($defaults, $sessionConfig);
			}
		}
		if (!isset($sessionConfig['ini']['session.cookie_secure']) && env('HTTPS')) {
			$sessionConfig['ini']['session.cookie_secure'] = 1;
		}
		if (isset($sessionConfig['timeout']) && !isset($sessionConfig['cookieTimeout'])) {
			$sessionConfig['cookieTimeout'] = $sessionConfig['timeout'];
		}
		if (!isset($sessionConfig['ini']['session.cookie_lifetime'])) {
			$sessionConfig['ini']['session.cookie_lifetime'] = $sessionConfig['cookieTimeout'] * 60;
		}
		if (!isset($sessionConfig['ini']['session.name'])) {
			$sessionConfig['ini']['session.name'] = $sessionConfig['cookie'];
		}
		if (!empty($sessionConfig['handler'])) {
			$sessionConfig['ini']['session.save_handler'] = 'user';
		}
		if (!isset($sessionConfig['ini']['session.gc_maxlifetime'])) {
			$sessionConfig['ini']['session.gc_maxlifetime'] = $sessionConfig['timeout'] * 60;
		}
		if (!isset($sessionConfig['ini']['session.cookie_httponly'])) {
			$sessionConfig['ini']['session.cookie_httponly'] = 1;
		}
	
		if (empty($_SESSION)) {
			if (!empty($sessionConfig['ini']) && is_array($sessionConfig['ini'])) {
				foreach ($sessionConfig['ini'] as $setting => $value) {
					if (ini_set($setting, $value) === false) {
						throw new CakeSessionException(sprintf(
								__d('cake_dev', 'Unable to configure the session, setting %s failed.'),
								$setting
						));
					}
				}
			}
		}
		if (!empty($sessionConfig['handler']) && !isset($sessionConfig['handler']['engine'])) {
			call_user_func_array('session_set_save_handler', $sessionConfig['handler']);
		}
		if (!empty($sessionConfig['handler']['engine'])) {
			$handler = self::_getHandler($sessionConfig['handler']['engine']);
			session_set_save_handler(
					array($handler, 'open'),
					array($handler, 'close'),
					array($handler, 'read'),
					array($handler, 'write'),
					array($handler, 'destroy'),
					array($handler, 'gc')
			);
		}
		Configure::write('Session', $sessionConfig);
		self::$sessionTime = self::$time + ($sessionConfig['timeout'] * 60);
	}*/
	
	/**
	 * Find the handler class and make sure it implements the correct interface.
	 *
	 * @param string $handler
	 * @return void
	 * @throws CakeSessionException
	 *
	protected static function _getHandler($handler) {
		list($plugin, $class) = pluginSplit($handler, true);
		App::uses($class, $plugin . 'Model/Datasource/Session');
		if (!class_exists($class)) {
			throw new CakeSessionException(__d('cake_dev', 'Could not load %s to handle the session.', $class));
		}
		$handler = new $class();
		if ($handler instanceof CakeSessionHandlerInterface) {
			return $handler;
		}
		throw new CakeSessionException(__d('cake_dev', 'Chosen SessionHandler does not implement CakeSessionHandlerInterface it cannot be used with an engine key.'));
	}*/
	
	protected function defaults() {
		return [
			'cookie'	=> 'SPEEDY_PHP',
			'timeout'	=> 240
		];
	}
	
	/**
	 * Get one of the prebaked default session configurations.
	 *
	 * @param string $name
	 * @return boolean|array
	 *
	protected static function _defaultConfig($name) {
		$defaults = array(
				'php' => array(
						'cookie' => 'CAKEPHP',
						'timeout' => 240,
						'ini' => array(
								'session.use_trans_sid' => 0,
								'session.cookie_path' => self::$path
						)
				),
				'cake' => array(
						'cookie' => 'CAKEPHP',
						'timeout' => 240,
						'ini' => array(
								'session.use_trans_sid' => 0,
								'url_rewriter.tags' => '',
								'session.serialize_handler' => 'php',
								'session.use_cookies' => 1,
								'session.cookie_path' => self::$path,
								'session.auto_start' => 0,
								'session.save_path' => TMP . 'sessions',
								'session.save_handler' => 'files'
						)
				),
				'cache' => array(
						'cookie' => 'CAKEPHP',
						'timeout' => 240,
						'ini' => array(
								'session.use_trans_sid' => 0,
								'url_rewriter.tags' => '',
								'session.auto_start' => 0,
								'session.use_cookies' => 1,
								'session.cookie_path' => self::$path,
								'session.save_handler' => 'user',
						),
						'handler' => array(
								'engine' => 'CacheSession',
								'config' => 'default'
						)
				),
				'database' => array(
						'cookie' => 'CAKEPHP',
						'timeout' => 240,
						'ini' => array(
								'session.use_trans_sid' => 0,
								'url_rewriter.tags' => '',
								'session.auto_start' => 0,
								'session.use_cookies' => 1,
								'session.cookie_path' => self::$path,
								'session.save_handler' => 'user',
								'session.serialize_handler' => 'php',
						),
						'handler' => array(
								'engine' => 'DatabaseSession',
								'model' => 'Session'
						)
				)
		);
		if (isset($defaults[$name])) {
			return $defaults[$name];
		}
		return false;
	}*/
	
	/**
	 * Helper method to start a session
	 *
	 * @return boolean Success
	 *
	protected static function start() {
		
		return true;
	}*/
	
	/**
	 * Helper method to create a new session.
	 *
	 * @return void
	 */
	protected function checkValid() {
		if (!$this->started() && !$this->start()) {
			$this->valid = false;
			return false;
		}
		if ($config = $this->get('Config')) {
			$sessionConfig = Config::read('Session');
	
			if ($this->validAgentAndTime()) {
				$this->set('Config.time', $this->sessionTime);
				/*if (isset($sessionConfig['autoRegenerate']) && $sessionConfig['autoRegenerate'] === true) {
					$check = $config['countdown'];
					$check -= 1;
					self::write('Config.countdown', $check);
	
					if ($check < 1) {
						self::renew();
						self::write('Config.countdown', self::$requestCountdown);
					}
				}*/
				$this->valid = true;
			} else {
				$this->destroy();
				$this->valid = false;
				$this->setError(1, 'Session Highjacking Attempted !!!');
			}
		} else {
			$this->set('Config.userAgent', $this->_userAgent);
			$this->set('Config.time', $this->sessionTime);
			//$this->set('Config.countdown', self::$requestCountdown);
			$this->valid = true;
		}
	}
	
	/**
	 * Restarts this session.
	 *
	 * @return void
	 */
	public function renew() {
		if (session_id()) {
			if (session_id() != '' || isset($_COOKIE[session_name()])) {
				setcookie(Config::read('Session.cookie'), '', time() - 42000, $this->path);
			}
			session_regenerate_id(true);
		}
	}
	
	/**
	 * Helper method to set an internal error message.
	 *
	 * @param integer $errorNumber Number of the error
	 * @param string $errorMessage Description of the error
	 * @return void
	 */
	protected function setError($errorNumber, $errorMessage) {
		if ($this->error === false) {
			$this->error = array();
		}
		$this->error[$errorNumber] = $errorMessage;
		$this->lastError = $errorNumber;
	}
	
	/**
	 * SessionHandlerInterface Class methods
	 */
	
	/**
	 * Close session
	 */
	public function close() {
		return true;
	}
	
	/**
	 * Helper method to destroy invalid sessions.
	 *
	 * @return void
	 */
	public function destroy($key = null) {
		if ($key) return $this->delete($key);
		if ($this->started()) {
			session_destroy();
		}
		$this->clear();
	}
	
	/**
	 * Clean up expired session
	 */
	public function gc($maxlifetime) {
		return true;
	}
	
	/**
	 * Opens a session
	 */
	public function open($save_path, $name) {
		return true;
	}
	
	public function read($session_id) {
		return $this->get($session_id);
	}
	
	public function write($session_id, $session_data) {
		return (($this->set($session_id, $session_data)) !== false) ? true : false;
	}
	
}

?>