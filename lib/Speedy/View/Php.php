<?php 
namespace Speedy\View;

use \Speedy\View\Base;
use \Speedy\Loader;
use \Speedy\Http\Exception as HttpException;
use \Speedy\View;

class Php extends Base {

	/**
	 * Render the template
	 * @param string $path
	 */
	public function renderTemplate($path = null) {
		extract($vars);
		include_once $path;
		return;
	}
	
}
?>