<?php 
namespace Speedy\Utility\Logger;

import('speedy.utility.logger.base');

defined("STDOUT") or define("STDOUT", fopen("php://stdout", "w"));


class Console extends Base {
	
	public function printLn($msg, $type = self::INFO) {
		fwrite(STDOUT, $msg . "\n");
	}
	
	
}

?>