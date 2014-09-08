<?php 
namespace Speedy {

	
	use Speedy\Config;
	use Speedy\Router;
	use Speedy\Utility\Inflector;
	use Speedy\Exception\Error as ErrorException;
	use Speedy\Middleware\Stack as MiddlewareStack;
	use Speedy\Middleware\Asset as MiddlewareAsset;
	use Speedy\Logger;
	use Speedy\Error;
	
	class App extends Object {
		
		protected static $_instance = null;
		
		/**
		 * @var Speedy\Request
		 * @deprecated
		 */
		protected $_request;
		
		/**
		 * @var Speedy\Router
		 */
		private $_router;

		/**
		 * @var Whoops/Run
		 */
		private $_whoops;
		
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
			
			$this->setNs(Inflector::underscore($this->name()));

			$loader = Loader::instance();
			$loader->registerNamespace('config', CONFIG_PATH);
			$this->addPackage($this->name());
			self::_setInstance($this);
			
			$envConfigPath	= CONFIG_PATH . DS . 'environments' . DS . SPEEDY_ENV . '.php';
			if (file_exists($envConfigPath)) {
				require_once $envConfigPath;
			}
			
			$this->setMiddlewareStack(new MiddlewareStack($this));
			if (!empty($this->_middlewares)) {
				$this->middlewareStack()->addFromArray($this->_middlewares);
			}
			$this->middlewareStack()->add(new MiddlewareAsset($this->middlewareStack()));
		}

		public function addPackage($name) {
			$inflected = Inflector::underscore($name);

			$loader = Loader::instance();
			$loader->pushPathToNamespace("$inflected.controllers",	APP_PATH . DS . $name . DS . 'Controllers');
			$loader->pushPathToNamespace("$inflected.models", 		APP_PATH . DS . $name . DS . 'Models');
			$loader->pushPathToNamespace("$inflected.helpers", 		APP_PATH . DS . $name . DS . 'Helpers');
			$loader->pushPathToNamespace("$inflected.assets", 		APP_PATH . DS . $name . DS . 'Assets');
			$loader->pushPathToNamespace("$inflected.views", 		APP_PATH . DS . $name . DS . 'Views');
			$loader->pushPathToNamespace($inflected, APP_PATH . DS . $name);
			//$loader->registerNamespace('sprockets', VENDOR_PATH . DS . 'SpeedyPHP' . DS . 'Sprockets'); 
			
			$loader->setAliases(array(
				'views'			=> ["$inflected.views"],
				'helpers'		=> ["$inflected.helpers"],
				'controllers'	=> ["$inflected.controllers"],
				'models'		=> ["$inflected.models"],
				'assets'		=> ["$inflected.assets"],
			));
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
				$config = Config::instance();
				$this->setConfig($config);
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
			
			Router::instance()
				->draw('\Config\Routes');
			
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
			$errorReporting = $this->config('errors.report');
			$errorLevel = $this->config('errors.reporting_level');
			if ($errorReporting) {
				error_reporting(!empty($errorLevel) ? $errorLevel : E_ALL ^ E_NOTICE);

				$this->_whoops = new \Whoops\Run();
				$pageHandler = new \Whoops\Handler\PrettyPageHandler();
				$pageHandler->addDataTable("Request Params", $this->request()->params());
				$this->_whoops->pushHandler($pageHandler);
				$this->_whoops->register();
			} else {
				error_reporting(0);
			}

			try {
				$this->stack()->run();
			} catch (\Exception $e) {
				Logger::error($e->getMessage());
				new Error($e);
			}
		}

		/**
		 * Exception handler
		 * @param $e Exception
		 */
		private function handleExceptions($e) {
			// Check environment
			// Check
			throw $e;
		}
		
		public function call() {
			$response = Dispatcher::run($this->router());

			// Clear output buffer to ensure the response formatting is maintained
			if ( ob_get_level() !== 0 ) {
				ob_clean();
			}

			echo $response;
		}
		
		/**
		 * Getter for router
		 * @return \Speedy\Router
		 */
		public function router() {
			if (!$this->_router) {
				$router	= Router::instance();
				$this->_setRouter($router);
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
		 * @deprecated
		 */
		public function _request() {
			return Request::instance();
		}
		
		public function handleError($errno, $errstr = '', $errfile = '', $errline = '') {
			if ( error_reporting() & $errno ) {
				throw new \Speedy\Exception\Error($errstr, $errno, 0, $errfile, $errline);
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
			'<div class="description"><p>[Error Number: %s] %s in %s on line %s</p></div>',
			'<div class="stack"><pre>%s</pre></div>',
			'<div class="params">',
			'<h2>Parameters</h2>',
			'<h3>GET</h3>',
			'<pre>%s</pre>',
			'<h3>POST</h3>',
			'<pre>%s</pre>',
			'<h3>FILES</h3>',
			'<pre>%s</pre>',
			'<h3>SERVER</h3>',
			'<pre>%s</pre>',
			'</div>',
			'</body>',
			'</html>'
			];
			$html = implode("\n", $html);
			return sprintf($html, 
					$e->getCode(), 
					$e->getMessage(), 
					$e->getFile(), 
					$e->getLine(), 
					$e->getTraceAsString(),
					print_r($_GET, true),
					print_r($_POST, true),
					print_r($_FILES, true),
					print_r($_SERVER, true));
		}
		
		public function cleanBuffer() {
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

}

namespace {

	function debug($obj) {
		echo "<pre>";
		if (is_array($obj)) {
			print_r($obj);
		} else {
			var_dump($obj);
		}
		echo "</pre>";
	}
	
	function rglob($pattern='*', $flags = 0, $path='') {
		$paths  = glob($path.'*', GLOB_MARK|GLOB_ONLYDIR|GLOB_NOSORT|GLOB_BRACE);
        $files  = glob($path.$pattern, $flags);
        if (!is_array($files))
		    $files  = [];

        foreach ($paths as $path) {
            $add    = rglob($pattern, $flags, $path);
			if (!is_array($add))
  			   	$add = [];

            $files  = array_merge($files,$add);
        }
        return $files;
	}
	
	function is_hash($var) {
		if (!is_array($var))
			return false;
	
		return array_keys($var) !== range(0,sizeof($var)-1);
	}

}

?>
