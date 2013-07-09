<?php 
namespace Speedy\Traits;


trait Convenience {
	
	public function respondsTo($method) {
		return method_exists($this, $method);
	}
	
	public function includes($name) {
		return property_exists($this, $name);
	}
	
}

?>
