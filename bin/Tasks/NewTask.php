<?php 

use Speedy\Utility\Inflector;


class NewTask extends Speedy\Task {

	public $alias = "new";
	
	private $_variables = array();
	
	private $_dir;

	public $description = 'starts a new project';
	
	
	public function __construct($args = null) {
		parent::__construct($args);
	}
	
	public function help() {
		$help = <<<EOF
Task to build a new project.
usage:
	    speedy new /path/to/project	
		
Commands:
test		-- Generators test
EOF;
		output($help);
	}
	
	public function defaultTask() {
		$dir	= $this->data(0);
		$base	= basename($dir);
		$namespace	= ucfirst(strtolower($base));
		$this->set('namespace',		$namespace);
		$this->set('lcNamespace',	strtolower($namespace));
		$this->set('controller',	'Application');
		
		if (empty($dir)) {
			$this->help();
			return 0;
		}
		
		output("Create project directory");
		mkdir($dir);
		
		output("Create Phakefile");
		$phakeContents	= $this->getTemplate("Phakefile.php");
		file_put_contents($dir . DS . 'Phakefile', $phakeContents);

		// $composerContents = $this->getTemplate('composer.json.php');
		// file_put_contents($dir . DS . 'composer.json', $composerContents);
		copy(SPEEDY_TEMPLATES . 'composer.json', $dir . DS . 'composer.json');
		
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
	
	public function get($name) {
		return (isset($this->_variables[$name])) ? $this->_variables[$name] : null;
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

	public function getNamespace() {
		return $this->get('namespace');
	}
	
	public function dir() {
		return $this->_dir;
	}
	
	public function getTemplate($tpl) {
		extract($this->variables());
		
		$content	= include SPEEDY_TEMPLATES . $tpl;
		return $content;
	}
	
	private function _requiredPaths() {
		return array(
			'app' => 'app',
			'assets'		=> 'app' . DS . $this->getNamespace() . DS . 'Assets',
			'controllers' 	=> 'app' . DS . $this->getNamespace() . DS . 'Controllers',
			'helpers'		=> 'app' . DS . $this->getNamespace() . DS . 'Helpers',
			'models'		=> 'app' . DS . $this->getNamespace() . DS . 'Models',
			'views'			=> 'app' . DS . $this->getNamespace() . DS . 'Views',
			'layouts'		=> 'app' . DS . $this->getNamespace() . DS . 'Views' . DS . 'layouts',
			'config'		=> 'config',
			'environments'	=> 'config' . DS . 'environments',
			'db'	=> 'db',
			'migrate'	=> 'db' . DS . 'migrate',
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
				'Routes.php'=> 'Routes.php',
				'database.json'	=> 'configuration' . DS . 'database.php'
			),
			'environments'	=> array(
				'development.php'	=> 'configuration' . DS . 'development.php',
				'production.php'	=> 'configuration' . DS . 'production.php',
				'test.php'	=> 'configuration' . DS . 'test.php'
			),
			'public'	=> array(
				'index.php'	=> 'index.php',
				'defines.php' => 'defines.php',
				'debug.php' => 'debug.php'
			),
			'controllers' => array(
				'Application.php'	=> 'AppController.php'
			),
			'layouts'	=> array(
				'application.php.html'	=> 'views' . DS . 'layout.php'
			)
		);
		
		return ($dir && isset($files[$dir])) ? $files[$dir] : null;
	}
	
}
