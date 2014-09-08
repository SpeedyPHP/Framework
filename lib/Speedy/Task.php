<?php 
namespace Speedy;


class Task {
	
	public $name;

	public $description;

	/**
	 * @var \cli\Arguments
	 */
	protected $args;
	

	public function __construct(\cli\Arguments $args) {
		$this->name	= strtolower(get_class());
		$this->args = $args;
		$this->setup();
	}
	
	/**
	 * Overwrite this function when subclassing
	 */
	public function setup() {}
	
	public function help() {}
	
	public function defaultTask() {}
	
	public function run() {
		$command = $this->data(1) . "Task";
		
		if (!$command) {
			$this->help();
			return 0;
		}
		
		$tasks = $this->_getTasks();
		if (in_array($command, $tasks)) {
			return $this->{$command}();
		} else {
			return $this->defaultTask();
		}
	}
	
	/**
	 * Get current classes tasks
	 * @return array 
	 */
	protected function _getTasks() {
		$methods = get_class_methods($this);
		return array_filter($methods, array($this, 'filterArray'));
	}
	
	public function filterArray($value) {
		return preg_match("/^[\w]+Task$/", $value);
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function argsCount() {
		return count($this->args);
	}
	
}


