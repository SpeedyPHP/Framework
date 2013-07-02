<?php 
namespace Speedy\Orm;

use \Speedy\Object;

abstract class Base extends Object {
	
	abstract public static function setup(\Speedy\Config $config);

}

?>
