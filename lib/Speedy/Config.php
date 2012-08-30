<?php 
namespace Speedy {
	use Speedy\Config\Exception as CException;
	
	
	class Config extends Singleton {
	
		/**
		 * Database configurations
		 * @var array
		 */
		protected $_db = array();
	
		/**
		 * Current environment
		 * @var string
		 */
		protected $_environment;
	
		/**
		 * Paths
		 * @var array
		 */
		protected $_paths	= array(
				'controllers'	=> array(),
				'helpers'		=> array(),
				'models'		=> array(),
				'views'			=> array()
		);
	
		/**
		 * List of view renders
		 * @var array
		 */
		protected $_renders	= array();
	
	
	
		public function __construct() {
			$dbJsonPath	= CONFIG_PATH . DS . 'database.json';
			if (!file_exists($dbJsonPath)) {
				throw new CException("Database configuration could not be found");
			}
	
			$this->setDb(json_decode_file($dbJsonPath, true));
		}
	
		/**
		 * Getter for renderer
		 * @return array
		 */
		public function renderers() {
			return $this->_renders;
		}
	
		/**
		 * Returns renderer for builder
		 * @param string $builder
		 */
		public function renderer($builder) {
			return (isset($this->_renders[$builder])) ? $this->_renders[$builder] : null;
		}
	
		/**
		 * Add a renderer to the stack
		 * @param string $format
		 * @param string $namespace
		 * @return \Speedy\Config
		 */
		public function addRenderer($format, $namespace) {
			$this->_renders[$format]	= $namespace;
			return $this;
		}
	
		/**
		 * Getter for environment
		 * @return string
		 */
		public function environment() {
			if (!$this->_environment) {
				$this->_environment = (isset($_ENV['SPEEDY_ENVIRONMENT'])) ? $_ENV['SPEEDY_ENVIRONMENT'] : 'development';
			}
	
			return $this->_environment;
		}
	
		/**
		 * Alias for environment method
		 * @return string
		 */
		public function env() {
			return $this->environment();
		}
	
		/**
		 * Getter for database settings
		 * @param string $setup
		 * @return array
		 */
		public function db($env = null) {
			return ($env) ? $this->_db[$env] : $this->_db;
		}
	
		/**
		 * Converts db array into string
		 * @param string $setup
		 */
		public function dbString($setup) {
			$options	= $this->db($setup);
	
			extract($options);
			$string	= '';
	
			switch (strtolower($adapter)) {
				case 'mysql':
				default:
					$string .= $adapter;
					break;
			}
	
			$string .= "://{$username}";
			if (isset($password) && strlen($password) > 0)
				$string .= ":{$password}";
			$string .= "@{$host}/{$database}";
			
			return $string;
		}
	
		/**
		 * Returns array of all db configs as string
		 * @return array
		 */
		public function dbStrings() {
			$connections	= array();
			foreach ($this->db() as $type => $conf) {
				$connections[$type]	= $this->dbString($type);
			}
	
			return $connections;
		}
	
		public function set($name, $value) {
			return $this->setData($name, $value);
		}
	
		public function get($name) {
			return $this->data($name);
		}
	
		/**
		 * Setter for environment
		 * @param string $env
		 * @return \Speedy\Config
		 */
		protected function setEnvironment($env) {
			$this->_environment	= $env;
			return $this;
		}
	
		/**
		 * Setter for database
		 * @param array $db
		 * @return \Speedy\Config
		 */
		protected function setDb($db) {
			$this->_db	= $db;
			return $this;
		}
	
	}	
}


namespace {
	
	function json_decode_file($file, $array = false) {
		if (!file_exists($file)) {
			trigger_error("Unable to find file $file in json_decode_file");
			return null;
		}
		
		$contents = file_get_contents($file);
		return json_decode($contents, $array);
	}
	
}

?>