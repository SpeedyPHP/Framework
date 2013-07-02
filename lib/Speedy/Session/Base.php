<?php 
namespace Speedy\Session;


abstract class Base implements \SessionHandlerInterface {
	
	abstract public function __construct();
	abstract public function start();
	abstract public function has($key);
	
}
?>
