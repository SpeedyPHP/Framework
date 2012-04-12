<?php 
namespace Vzed;

require_once "Loader.php";
import('vzed.object');
import('vzed.router');
import('vzed.dispatcher');
import('vzed.utility.inflector');

use \Vzed\Router;
use \Vzed\Utility\Inflector;
use \Vzed\Config;

class App extends Object {
	
	protected static $_instance = null;
	
	protected $_request;
	
	private $_router;
	
	/**
	 * Define the name which will be the namespace of the app
	 * @var string
	 */
	protected $_name;
	
	/**
	 * Defined namespace of app
	 * @var string
	 */
	protected $_ns;
	
	/**
	 * Config of the app
	 * @var \Vzed\Config
	 */
	protected $_config;
	
	
	
	
	/**
	 * Set the singleton app
	 * @param \Vzed\App $app
	 * @return \Vzed\App $app
	 */
	protected static function _setInstance(\Vzed\App $app) {
		self::$_instance = $app;
		return $app;
	}
	
	/**
	 * initiates the singleton class;
	 * @throws Exception
	 */
	private static function _init() {
		if (self::$_instance !== null) {
			throw new Exception('App class already has shared instance');
		}
		
		$class	= get_called_class();
		self::$_instance = new $class();
		
		return self::$_instance;
	}
	
	/**
	 * Get the singleton instance
	 * @throws \Vzed\Exception
	 * @return \Vzed\App
	 */
	public static function instance() {
		if (self::$_instance == null) {
			self::_init();
		}
		
		return self::$_instance;
	}
	
	/**
	 * Strap together all resources
	 */
	public function __construct() {
		if (!$this->name()) {
			throw new Exception("Subclass of App needs property \$_name defined.");
		}
		
		$this->_setRequest(new Request());
		$this->setNs(Inflector::underscore($this->name()));
		
		$loader = Loader::instance();
		$loader->registerNamespace($this->ns(), APP_PATH);
		$loader->registerNamespace("{$this->ns()}.config", CONFIG_PATH);
		$loader->registerNamespace('active_record', VZED_PATH . DS . 'activerecord');
		
		$config	= $this->config();
		
		self::_setInstance($this);
	}
	
	/**
	 * Setter for config
	 * @param \Vzed\Config $config
	 * @return \Vzed\App
	 */
	protected function setConfig(\Vzed\Config &$config) {
		$this->_config =& $config;
		return $this;
	}
	
	public function config() {
		if (!$this->_config) {
			$this->setConfig(Config::instance());
		}
		
		return $this->_config;
	}
	
	/**
	 * Setter for namespace
	 * @param string $ns
	 */
	protected function setNs($ns) {
		$this->_ns	= strtolower($ns);
		return $this;
	}
	
	/**
	 * Getter for namespace
	 */
	public function ns() {
		return (!empty($this->_ns)) ? $this->_ns : null;
	}
	
	/**
	 * Getter for name
	 */
	public function name() {
		return (!empty($this->_name)) ? $this->_name : null;
	}

	/**
	 * Bootstrap all application
	 * @return $this;
	 */
	public function bootstrap() {
		$methods = $this->bootstrapMethods();
		foreach ($methods as $method) {
			$this->{$method}();
		}
		
		$router	= Router::instance();
		$router
			->setRequest($this->_request())
			->draw($this->name() . '\Config\Routes');
		
		return $this;
	}
	
	public function run() {
		Dispatcher::run($this->router());
	}
	
	/**
	 * Getter for just bootstrap methods
	 * @return array of bootstrap methods
	 */
	private function bootstrapMethods() {
		$methods = get_class_methods($this);
		return array_filter($methods, array($this, 'filterMethods'));
	}
	
	/**
	 * Filter methods array for initMethods only 
	 * @param array $value
	 */
	public function filterMethods($value) {
		return preg_match("/^init[A-Z]{1,}[\w]+$/", $value);
	}
	
	/**
	 * Setter for router
	 * @param \Vzed\Router $router
	 */
	private function _setRouter(&$router) {
		$this->_router	=& $router;
		return $this;
	}
	
	/**
	 * Getter for router
	 * @return \Vzed\Router
	 */
	public function router() {
		if (!$this->_router) {
			$this->_setRouter(Router::instance());
		}
		
		return $this->_router;
	}
	
	/**
	 * Setter for request
	 * @param \Vzed\Request $request
	 * @return $this
	 */
	private function _setRequest($request) {
		$this->_request	= $request;
		return $this;
	}
	
	/**
	 * Getter for request property
	 */
	public function _request() {
		return $this->_request;
	}
	
	/**
	 * Static Getter for request property
	 */
	public static function request() {
		$self	= self::instance();
		return $self->_request();
	}
	
}

?>