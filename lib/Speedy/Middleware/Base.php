<?php 
namespace Speedy\Middleware;


abstract class Base {
	
	/**
	 * @var object \Speedy\Middleware\Stack
	 */
	public $stack;
	

	public function __construct(\Speedy\Middleware\Stack $stack) {
		$this->setStack($stack)->add($this);
		return $this;
	}
	
	public function setStack(\Speedy\Middleware\Stack $stack) {
		$this->stack = $stack;
		return $this->stack;
	}
	
	public function stack() {
		return $this->stack;
	}
	
	public function next() {
		return $this->stack->next();
	}
	
	abstract public function call();
	
}

?>