<?php

class Speedy extends \Cilex\Application {
	
	private $_tasks = array();
	
	
	public function renderTasksScreen() {
		\cli\line("Commands:");
		foreach ($this->_tasks as $task) {
			$name = isset($task->alias) ? $task->alias : $task->name;
			\cli\line("{$name}\t-- {$task->description}");
		}
	}

	public function help() {
		/*$help = <<<EOF
-h/--help		This help menu
test			Test unit
g			Generators
new 			Generate new project
EOF; */
		// $this->args->parse();
		\cli\line($this->args->getHelpScreen());
		$this->renderTasksScreen();
	}
	
	public function bootstrap() {
		$this->args->addFlag(array('help', 'h'), 'Show this help screen');

		\cli\line('------------------------------------------------');
		\cli\line('------------------ SpeedyPHP -------------------');
		\cli\line('------------------------------------------------');

		if (APP_LOADED) $this->_loadApp();
		$this->_loadTasks();
		// output();
		
		return $this;
		/*$this->args->parse();
		echo json_encode($this->args->getInvalidArguments());
		if ($this->argsCount() < 2) {
			$this->help();
			return 0;
		}
		
		$taskName = $this->data(0);
		if (!$this->hasTask($taskName)) return -1;
		
		$task = $this->getTask($taskName);
		
		return $task->run();*/
	}
	
	/**
	 * Attempt to load app in current directory
	 */
	private function _loadApp() {
		$app	= App::instance();
		\cli\line("Loaded {$app->name()} from path " . CONFIG_PATH . "\n");
	}
	
	private function _loadTasks() {
		$directories = array(dirname(__FILE__) . DS . "Tasks");

		foreach ($directories as $dir) {
			if (!is_dir($dir)) continue;
			
			foreach (glob($dir . DS . '*.php') as $file) {
				require_once $file;
				$info	= pathinfo($file);
				$class 	= $info['filename'];
					
				$obj = new $class($this->args);
					
				if (!empty($obj->alias))
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
	// $app = new \Cilex\Application('Speedy');
	$app = new Speedy('Speedy');
	$app->run();
}

