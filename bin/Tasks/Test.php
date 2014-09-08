<?php 

class Test extends Speedy\Task {

	public $description = 'run test units';
	
	public function help() {
		$help = <<<EOF
Test harness kit. 
usage:
    speedy test [classname]
		
EOF;
		output($help);
	}
	
	public function defaultTask() {
		$class = ucfirst($this->data(0));
		$fileName = $class . ".php";
		$filePath = SPEEDY_CLI . DS . "tests" . DS . $fileName;
		
		if (!file_exists($filePath)) {
			return $this->error(1);
		}
		
		require_once $filePath;
		$obj = new $class();
		$obj->setup();
		$obj->test();
		
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
