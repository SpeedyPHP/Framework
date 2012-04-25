<?php 
namespace Vzed\View;

use \Vzed\View\Base;
use \Vzed\Loader;
use \Vzed\Http\Exception as HttpException;
use \Vzed\View;

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