<?php 
namespace Vzed\Orm;

use \Vzed\Object;

abstract class Base extends Object {
	
	abstract public static function setup(\Vzed\Config $config);

}

?>