<?php 
namespace Speedy\Utility\Logger;

defined("STDOUT") or define("STDOUT", fopen("php://stdout", "w"));


class Console extends Base {
	
	public function printLn($msg, $type = self::INFO) {
		fwrite(STDOUT, $msg . "\n");
	}
	
	
}

?>
