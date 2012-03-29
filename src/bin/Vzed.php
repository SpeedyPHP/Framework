<?php
require_once VECTOR_PATH . 'Loader.php';
Vzed\import('vzed.task');

class Vzed extends Vzed\Task {
	
	private $_tasks = array();
	
	
	public function help() {
		$help = <<<EOF
-h/--help		This help menu
test			Test unit
EOF;
		output($help);
	}
	
	public function main() {
		$out = <<<EOF
------------------------------------------------
--------------- Vectorized PHP -----------------
------------------------------------------------
		
EOF;
		output($out);
		$this->_loadTasks();
		
		if ($this->argsCount() < 1) {
			$this->help();
			return 0;
		}
		
		$taskName = $this->getData(0);
		if (!$this->hasTask($taskName)) return -1;
		
		$task = $this->getTask($taskName);
		
		return $task->run();
	}
	
	private function _loadTasks() {
		$directories = array(dirname(__FILE__) . DS . "Tasks");
		
		foreach ($directories as $dir) {
			if (!is_dir($dir)) continue;
			
			if ($dh = opendir($dir)) {
				$data = $this->getData();
				if ($data) array_shift($data);
				
				while (($file = readdir($dh)) !== false) {
					if (!preg_match("/^([\w_]+)\.php$/i", $file, $matches)) continue;
					
					require_once $dir . DS . $file;
					$class = $matches[1];
					
					$obj = new $class();
					$obj->setData($data);
					
					$this->_addTask(($obj->alias) ? $obj->alias : strtolower($class), $obj);
				}
				
				closedir($dh);
			}
		}
	}
	
	private function _addTask($task, $obj) {
		$this->_tasks[$task] = $obj;
	}
	
	public function hasTask($name) {
		return isset($this->_tasks[$name]);
	}
	
	public function getTask($name) {
		return $this->_tasks[$name];
	}
	
}

function output($str = "") {
	fwrite(STDOUT, $str . "\n");
}

function fecho($str) {
	if (is_array($str))
		print_r($str);
	else 
		print $str . "\n";
}

if (php_sapi_name() == 'cli') {
	$self = new Vzed($argv);

	$return = $self->main();

	exit($return);
}

?>