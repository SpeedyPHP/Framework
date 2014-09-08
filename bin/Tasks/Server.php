<?php 

class Server extends Speedy\Task {
	
	public $alias = "s";

	public $description = 'start server';
	
	public function defaultTask() {
		$php	= chop(`which php`);
		$curDir	= chop(`pwd`);
		$host	= "localhost";
		$port	= 8000;
		$docroot= $curDir . DS . 'public';
		// $index	= $docroot . DS . 'index.php';
		$router	= $docroot . DS . 'debug.php';
		 
		$cmd	= "{$php} -S {$host}:{$port} -t {$docroot} {$router}";

		output("Starting PHP Server");
		output("Listening http://{$host}:{$port}");
		$handle = popen($cmd, "r");
		
		while ($read = fread($handle, 2096)) {
			output($read);
		}
		
		pclose($handle);
		
		return 0;
	}
	
	public function error($number) {
		switch($number) {
			case 1:
				output("File not found in tests path");
				break;
			
			default:
				output("Unknown error occured");
				break;
		}
	}
	
	
}

?>
