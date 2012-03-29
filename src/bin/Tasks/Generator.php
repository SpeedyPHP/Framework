<?php 

class Generator extends Vzed\Task {
	
	private $_generators = array(
		'test' => 'generateTest' 
	);
	
	
	public function help() {
		$help = <<<EOF
Generator kit. Use without command for help or with -h option.
usage:
	    vzed generator [command]	
		
Commands:
test		-- Generators test
EOF;
		output($help);
	}
	
	public function hasGenerator($name) {
		return ($this->_generators[$name]) ? true : false;
	}
	
	public function getGenerator($name) {
		return $this->_generators[$name];
	}
	
	public function getGenerators() {
		return $this->_generators;
	}
	
	public function defaultTask() {
		$command	= $this->getData(0);
		
		if (!$this->hasGenerator($command)) {
			output("No generator found for: $command");
			return 1;
		}
		
		$callback	= $this->getGenerator($command);
		if (is_array($callback)) {
			// TODO: add array callback functionality
		} else {
			$callback	= array($this, $callback);
			return call_user_func($callback);
		}
		
		return 0;
	}
	
	public function testTask() {
		var_dump($this->getData());
		//if ()
	}
	
	public function generateTest() {
		$class = $this->getData(1);
		output("Generating test harness for: $class");
		
		$content= $this->_testTemplate(array('className' => $class));
		// TODO: fix later to user current project directory
		$file	= VECTOR_CLI . DS . 'tests' . DS . $class . ".php";
		$fh	= fopen($file, 'w');

		fwrite($fh, $content);
		fclose($fh);
		output("Saved the file");
	}
	
	/**
	 * Generator for test template
	 * @param array $variables
	 */
	private function _testTemplate(array $variables) {
		if (is_array($variables)) {
			extract($variables);
		}
		
		return <<<TPL
<?php
require_once VECTOR_PATH . "Loader.php";

\Vzed\import('vzed.test');				// import the test subclass
// \Vzed\import('vzed.object');			// import the class

class $className extends \Vzed\Test {
	
	private \$_instance;
	
	public function setup() {
		output("Setup the test here");
		\$this->_instance = new $className(); 
	}
	
	public function test() {
		\$instance =& \$this->_instance;
		
		output("Do test here");
	}
	
}
TPL;
	}
	
}

?>