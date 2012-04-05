<?php 

class NewTask extends Vzed\Task {

	public $alias = "new";
	
	private $_variables = array();
	
	private $_dir;
	
	
	public function __construct($args = null) {
		parent::__construct($args);
	}
	
	public function help() {
		$help = <<<EOF
Task to build a new project.
usage:
	    vzed new /path/to/project	
		
Commands:
test		-- Generators test
EOF;
		output($help);
	}
	
	public function defaultTask() {
		$dir	= $this->getData(0);
		$base	= basename($dir);
		$namespace	= ucfirst(strtolower($base));
		$this->set('namespace', $namespace);
		$this->set('lcNamespace', strtolower($namespace));
		
		if (empty($dir)) {
			$this->help();
			return 0;
		}
		
		output("Create project directory");
		mkdir($dir);
		
		$dir	.= DS;
		foreach ($this->_requiredPaths() as $type => $subDir) {
			output("Create $subDir");
			$path	= $dir . $subDir;
			mkdir($path);
			
			$files	= $this->_files($type);
			if (empty($files)) {
				continue;
			}
			
			foreach ($files as $toDir => $pathToTpl) {
				$contents	= $this->getTemplate($pathToTpl);
				
				file_put_contents($path . DS . $toDir, $contents);
			}
		}
		
		return 0;
	}
	
	private function _setDir($dir) {
		$this->_dir = $dir;
		return $this;
	}
	
	private function set($name, $value) {
		$this->_variables[$name] = $value;
		return $this;
	}
	
	private function _setVariables($variables) {
		$this->_variables	= $variables;
		return $this;
	}
	
	public function variables() {
		return $this->_variables;
	}
	
	public function dir() {
		return $this->_dir;
	}
	
	public function getTemplate($tpl) {
		extract($this->variables());
		
		$content	= include VECTOR_TEMPLATES . $tpl;
		return $content;
	}
	
	private function _requiredPaths() {
		return array(
			'app' => 'app',
			'controllers' 	=> 'app' . DS . 'controllers',
			'helpers'		=> 'app' . DS . 'helpers',
			'models'		=> 'app' . DS . 'models',
			'views'			=> 'app' . DS . 'views',
			'config'		=> 'config',
			'environments'	=> 'config' . DS . 'environments',
			'db'	=> 'db',
			'migrate'	=> 'migrate',
			'lib'	=> 'lib',
			'tmp'	=> 'tmp',
			'log'	=> 'tmp' . DS . 'log',
			'cache'	=> 'tmp' . DS . 'cache',
			'sessions'	=> 'tmp' . DS . 'sessions',
			'public'	=> 'public',
			'test'	=> 'test'
		);
	}
	
	private function _files($dir) {
		$files 	= array(
			'config'	=> array(
				'App.php' => 'App.php',
				'Routes.php'=> 'Routes.php'
			),
			'public'	=> array(
				'index.php'	=> 'index.php'
			)
		);
		
		return ($dir) ? $files[$dir] : $files;
	}
	
}

?>