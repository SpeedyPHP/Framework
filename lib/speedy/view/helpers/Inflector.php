<?php 
namespace Speedy\View\Helpers;

use \Speedy\View\Exception as ViewException;
use \Speedy\View\Helpers\Exception;
use \Speedy\View\Helpers\Base;
use \Speedy\Utility\Inflector as UInflector;


class Inflector extends Base {
	
	public function pluralize($count = 0, $word) {
		return ($count > 0) ? UInflector::pluralize($word) : $word; 
	}
	
}
?>