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
	public function render($path = null) {
		$ns			= \App::instance()->ns();
		$options	= $this->options();
		$path		= ($path) ? $path : $this->path();
		$vars		= $this->vars();
	
		if (!file_exists($path)) {
			throw new HttpException('View found not found at ' . $path);
		}
	
		if ($options['layout']) {
			$layout	= 'layouts' . DS . $options['layout'];
			$vars['content_for_layout']	= $this->toString($path);
				
			unset($options['layout']);
			View::instance()->render($layout, $options, $vars, $this->getData());
		} else {
			extract($vars);
			include_once $path;
		}
	}
	
}
?>