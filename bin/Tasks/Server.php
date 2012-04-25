<?php 

class Server extends Speedy\Task {
	
	public $alias = "s";
	
	public function defaultTask() {
		$php	= chop(`which php`);
		$curDir	= chop(`pwd`);
		$host	= "localhost";
		$port	= 8000;
		$docroot= $curDir . DS . 'public';
		$index	= $docroot . DS . 'index.php';
		 
		$cmd	= "{$php} -S {$host}:{$port} -t {$docroot}";
		output("Starting PHP Server");
		output("Listening http://{$host}:{$port}");
		exec($cmd);
		
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