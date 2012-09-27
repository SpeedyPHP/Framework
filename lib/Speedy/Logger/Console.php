<?php 
namespace Speedy\Logger;


defined("STDOUT") or define("STDOUT", fopen("php://stdout", "w"));
class Console extends Base {
	
	public function add($msg) {
		$msg	= $this->cleanInput($msg);
		ob_start();
		echo $msg;
		$content	= ob_get_clean();
		
		fwrite(STDOUT, $content . "\n");
	}
	
	public function info($msg) {
		$this->add($msg);
	}
	
	public function debug($msg) {
		$msg	= $this->cleanInput($msg);
		$this->add($this->boldText('[DEBUG] ') . $msg);	
	}
	
	public function error($msg) {
		$msg	= $this->cleanInput($msg);
		$this->add($this->boldText('[ERROR] ') . $msg);
	}
	
	public function fatal($msg) {
		$msg	= $this->cleanInput($msg);
		$this->add($this->boldText('[FATAL] ') . $msg);
	}
	
	public function warn($msg) {
		$msg	= $this->cleanInput($msg);
		$this->add($this->boldText('[WARN] ') . $msg);
	}

	public function boldText($text) {
		return '\033[1m' . $text . '\033[0m';
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