<?php 
namespace Vzed;

use Vzed\Config\Exception as CException;


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
	
	
	
	public function __construct() {
		$dbYamlPath	= CONFIG_PATH . DS . 'database.yml';
		if (!file_exists($dbYamlPath)) {
			throw new CException("Database configuration could not be found");
		}
		
		$db	= yaml_parse_file($dbYamlPath);
	}
	
	protected function setEnvironment($env) {
		
	}
	
	/**
	 * Setter for database
	 * @param array $db
	 * @return \Vzed\Config
	 */
	protected function setDb(array $db) {
		$this->_db	= $db;
		return $this;
	}
	
	/**
	 * Getter for database settings
	 * @param string $setup
	 * @return array
	 */
	public function db($setup = null) {
		return (is_string($setup)) ? $this->_db[$setup] : $this->_db;
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
		
		$string .= "://{$username}:{$password}@{$host}/{$database}";
		return $string;
	}
}
?>