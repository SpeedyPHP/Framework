<?php 
namespace Speedy\View\Helpers;

use \Speedy\View\Exception as ViewException;
use \Speedy\View\Helpers\Exception;
use \Speedy\Utility\Inflector as UInflector;


trait Inflector {
	
	public function pluralize($count = 0, $word) {
		return ($count > 0) ? UInflector::pluralize($word) : $word; 
	}
	
}
?>