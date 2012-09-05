<?php 
namespace Speedy;


use \Speedy\Config;
use \Speedy\Router;
use \Speedy\Utility\Inflector;
use \Speedy\Exception\Error as ErrorException;
use \Speedy\Middleware\Stack as MiddlewareStack;
use \Speedy\Middleware\Asset as MiddlewareAsset;

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
	 * @var \Speedy\Config
	 */
	protected $_config;
	
	/**
	 * Orm bootstrap
	 * @var string
	 */
	protected $_orm;
	
	/**
	 * Middleware Stack
	 * @var object \Speedy\Middleware\Stack
	 */
	protected $_middlewareStack;
	
	/**
	 * List of middlewares 
	 * @var array
	 */
	protected $_middlewares = [];
	
	
	/**
	 * Set the singleton app
	 * @param \Speedy\App $app
	 * @return \Speedy\App $app
	 */
	protected static function _setInstance(\Speedy\App $app) {
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
	 * @throws \Speedy\Exception
	 * @return \Speedy\App
	 */
	public static function instance() {
		if (self::$_instance == null) {
			self::_init();
		}
		
		return self::$_instance;
	}
	
	/**
	 * Static Getter for request property
	 */
	public static function request() {
		$self	= self::instance();
		return $self->_request();
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
		$loader->registerNamespace("{$this->ns()}.config", CONFIG_PATH);
		$loader->registerNamespace("{$this->ns()}.controllers", [APP_PATH . DS . 'Controllers']);
		$loader->registerNamespace("{$this->ns()}.models", 		[APP_PATH . DS . 'Models']);
		$loader->registerNamespace("{$this->ns()}.helpers", 	[APP_PATH . DS . 'Helpers']);
		$loader->registerNamespace("{$this->ns()}.assets", 		[APP_PATH . DS . 'Assets']);
		$loader->registerNamespace("{$this->ns()}.views", 		[APP_PATH . DS . 'Views']);
		$loader->registerNamespace($this->ns(), APP_PATH);
		//$loader->registerNamespace('sprockets', VENDOR_PATH . DS . 'SpeedyPHP' . DS . 'Sprockets'); 
		
		$loader->setAliases(array(
			'views'			=> "{$this->ns()}.views",
			'helpers'		=> "{$this->ns()}.helpers",
			'controllers'	=> "{$this->ns()}.controllers",
			'models'		=> "{$this->ns()}.models",
			'assets'		=> "{$this->ns()}.assets",
		));
		
		self::_setInstance($this);
		
		$envConfigPath	= CONFIG_PATH . DS . 'environments' . DS . SPEEDY_ENV . '.php';
		if (file_exists($envConfigPath)) {
			require_once $envConfigPath;
		}
		
		$this->setMiddlewareStack(new MiddlewareStack($this));
		$this->middlewareStack()->add(new MiddlewareAsset($this->middlewareStack()));
		if (!empty($this->_middlewares)) {
			$this->middlewareStack()->addFromArray($this->_middlewares);
		}
		
		output("\nStarting new request");
		output("REQUEST: " . self::request()->url());
		output("METHOD: " . self::request()->method());
	}
	
	/**
	 * Getter for orm
	 * @return string of namespace for orm bootstrap
	 */
	public function orm() {
		return $this->_orm;
	}
	
	/**
	 * Getter for config object or config value
	 * @param string $name (optional)
	 */
	public function config($name = null) {
		if (!$this->_config) {
			$this->setConfig(Config::instance());
		}
		
		return ($name === null) ? $this->_config : $this->config()->data($name);
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
	 * Getter for middleware stack
	 * @return object \Speedy\Middleware\Stack
	 */
	public function middlewareStack() {
		return $this->_middlewareStack;
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
	
	/**
	 * Getter for middleware stack
	 * @return object \Speedy\Middleware\Stack
	 */
	public function stack() {
		return $this->_middlewareStack;
	}
	
	public function run() {
		set_error_handler([$this, 'handleError']);
		$this->stack()->run();
	}
	
	public function call() {
		try {
			$response = Dispatcher::run($this->router());
			echo $response;
		} catch (\Exception $e) {
			$this->cleanBuffer();
			echo $this->exceptionFormat($e);
		}
	}
	
	/**
	 * Getter for router
	 * @return \Speedy\Router
	 */
	public function router() {
		if (!$this->_router) {
			$this->_setRouter(Router::instance());
		}
		
		return $this->_router;
	}
	
	/**
	 * Filter methods array for initMethods only 
	 * @param array $value
	 */
	public function filterMethods($value) {
		return preg_match("/^init[A-Z]{1,}[\w]+$/", $value);
	}
	
	/**
	 * Getter for request property
	 */
	public function _request() {
		return $this->_request;
	}
	
	public function handleError($errno, $errstr = '', $errfile = '', $errline = '') {
		if ( error_reporting() & $errno ) {
			throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
		}
		return true;
	}
	
	/**
	 * Setter for config
	 * @param \Speedy\Config $config
	 * @return \Speedy\App
	 */
	protected function setConfig(\Speedy\Config &$config) {
		$this->_config =& $config;
		return $this;
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
	 * Setter for configurations
	 * @param string $name
	 */
	protected function configure($closure) {
		//return $this->config()->setup($closure);
		return $closure(Config::instance());
	}
	
	protected function exceptionFormat(\Exception $e) {
		$html = [
		'<html>',
		'<head>',
		'</head>',
		'<body>',
		'<div class="page-header">',
		'<h1>Exception Caught!</h1>',
		'</div>',
		'<div class="description"><p>%s %s in %s</p></div>',
		'<div class="stack"><pre>%s</pre></div>',
		'</body>',
		'</html>'
		];
		$html = implode("\n", $html);
		return sprintf($html, $e->getCode(), $e->getMessage(), $e->getFile(), $e->getTraceAsString());
	}
	
	protected function cleanBuffer() {
		if ( ob_get_level() !== 0 ) {
			ob_clean();
		}
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
	 * Setter for router
	 * @param \Speedy\Router $router
	 */
	private function _setRouter(&$router) {
		$this->_router	=& $router;
		return $this;
	}
	
	/**
	 * Setter for middleware stack
	 * @param \Speedy\Middleware\Stack $stack
	 */
	private function setMiddlewareStack(\Speedy\Middleware\Stack $stack) {
		$this->_middlewareStack = $stack;
		return $this;
	}
	
	/**
	 * Setter for request
	 * @param \Speedy\Request $request
	 * @return $this
	 */
	private function _setRequest($request) {
		$this->_request	= $request;
		return $this;
	}
}



?>