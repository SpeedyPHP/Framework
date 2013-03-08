<?php 
namespace Speedy\Logger;


use Speedy\Utility\File as FileUtility;

class File extends Base {

	private $_resource;

	
	public function __construct() {
		$dir = TMP_PATH . DS . 'log';
		if (!file_exists($dir)) {
			FileUtility::mkdir_p($dir);
		}
		
		$file = $dir . DS . SPEEDY_ENV;
		$this->_resource = fopen($file, 'a');
		if (!$this->_resource) {
			throw new Exception('Unable to open log file for writing');
		}
	}

	public function add($msg) {
		$content	= $this->cleanInput($msg);
		//ob_start();
		//echo $msg;
		//$content	= ob_get_clean();
		
		fwrite($this->_resource, $content . "\n");
	}
	
	public function info($msg) {
		$this->add($msg);
	}
	
	public function debug($msg) {
		$msg	= $this->cleanInput($msg);
		$this->add('[DEBUG] ' . $msg);	
	}
	
	public function error($msg) {
		$msg	= $this->cleanInput($msg);
		$this->add('[ERROR] ' . $msg);
	}
	
	public function fatal($msg) {
		$msg	= $this->cleanInput($msg);
		$this->add('[FATAL] ' . $msg);
	}
	
	public function warn($msg) {
		$msg	= $this->cleanInput($msg);
		$this->add('[WARN] ' . $msg);
	}
	
	public function __destruct() {
		self::add("\n\n");
	}
	
	/**
	 * Clean given variable into string
	 * @param mixed $msg
	 * @return string
	 */
	public function cleanInput($msg) {
		if (is_string($msg))
			return $msg;
		
		if (is_array($msg)) {
			$msg = print_r($msg, true);
		} else {
			ob_start();
			var_dump($msg);
			$msg	= ob_end_clean();
		}
		
		return $msg;
	}
}
?>