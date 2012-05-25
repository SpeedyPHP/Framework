<?php 
namespace Speedy\App;

use \Speedy\Object;

class Config extends Object {
	
	public static $_paths	= array();
	
	
	static public function getPath($name) {
		return self::$_paths[$name];
	}
	
	static public function setPath($name, array $paths) {
		self::$_paths[$name]	= $paths;
	}
	
}

?>