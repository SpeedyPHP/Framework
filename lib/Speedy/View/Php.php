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
	public function renderTemplate($path = null, $vars = []) {
		ob_start();
		extract($vars);
		include $path;
		$contents = ob_get_contents();
		ob_end_clean();
		
		return $contents;
	}
	
}
?>
