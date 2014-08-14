<?php

class Speedy extends Speedy\Task {
	
	private $_tasks = array();
	
	
	public function help() {
		$help = <<<EOF
-h/--help		This help menu
test			Test unit
g			Generators
new 			Generate new project
EOF;
		output($help);
	}
	
	public function main() {
		$out = <<<EOF
------------------------------------------------
------------------ SpeedyPHP -------------------
------------------------------------------------	
EOF;
		output($out);
		if (APP_LOADED) $this->_loadApp();
		$this->_loadTasks();
		output();
		
		if ($this->argsCount() < 1) {
			$this->help();
			return 0;
		}
		
		$taskName = $this->data(0);
		if (!$this->hasTask($taskName)) return -1;
		
		$task = $this->getTask($taskName);
		
		return $task->run();
	}
	
	/**
	 * Attempt to load app in current directory
	 */
	private function _loadApp() {
		$app	= App::instance();
		output("Loaded {$app->name()} from path " . CONFIG_PATH);
	}
	
	private function _loadTasks() {
		$directories = array(dirname(__FILE__) . DS . "Tasks");
		$data = $this->data();
		if ($data) array_shift($data);

		foreach ($directories as $dir) {
			if (!is_dir($dir)) continue;
			
			/*if ($dh = opendir($dir)) {				
				while (($file = readdir($dh)) !== false) {
					if (!preg_match("/^([\w_]+)\.php$/i", $file, $matches)) continue;
					
					require_once $dir . DS . $file;
					$class = $matches[1];
					
					$obj = new $class();
					$obj->setData($data);
					
					$this->_addTask(($obj->alias) ? $obj->alias : strtolower($class), $obj);
				}
				
				closedir($dh);
			}*/
			
			foreach (glob($dir . DS . '*.php') as $file) {
				require_once $file;
				$info	= pathinfo($file);
				$class 	= $info['filename'];
					
				$obj = new $class();
				$obj->setData($data);
					
				$this->_addTask(($obj->alias) ? $obj->alias : strtolower($class), $obj);
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

function fecho($str) {
	if (is_array($str))
		print_r($str);
	else 
		print $str . "\n";
}

if (php_sapi_name() == 'cli') {
	$self = new Speedy($argv);

	$return = $self->main();

	exit($return);
}

