<?php 
namespace Vzed;

use \Vzed\Loader;

class View {
	
	private $_controller;
	
	private $_templatePath;
	
	private $_options;
	
	private $_tplVars;
	
	
	/**
	 * View renderer
	 * @param \Vzed\Controller $controller
	 */
	public function __construct(\Vzed\Controller &$controller, $options = array()) {
		$this
			->setController($controller)
			->setOptions($options)
			->setTplVars($this->controller()->tplVars());
	}
	
	/**
	 * Setter for template vars
	 * @param array $tplVars
	 * @return \Vzed\View
	 */
	protected function setTplVars(array $tplVars) {
		$this->_tplVars	= $tplVars;
		return $this;
	}
	
	/**
	 * Getter for template variables
	 * @return array
	 */
	protected function tplVars() {
		return $this->_tplVars;
	}
	
	/**
	 * Setter for template variables
	 * @param string $name
	 * @param mixed $value
	 * @return \Vzed\View
	 */
	protected function set($name, $value) {
		$this->_tplVars[$name]	= $value;
		return $this;
	}
	
	/**
	 * Getter for options
	 * @return array options
	 */
	protected function options() {
		return $this->_options;
	}
	
	/**
	 * Setter options
	 * @param array $options
	 */
	protected function setOptions($options) {
		$this->_options	= $options;
		return $this;
	}
	
	/**
	 * Setter for controller
	 * @param unknown_type $controller
	 */
	private function setController(&$controller) {
		$this->_controller =& $controller;
		return $this;
	}
	
	/**
	 * Getter for the controller
	 * @return \Vzed\Controller
	 */
	protected function controller() {
		return $this->_controller;
	}
	
	/**
	 * Render the template
	 * @param string $template
	 */
	public function render($template = null) {
		$ns	= App::instance()->ns();
		$options	= $this->options();
		if (!$path) $path = $this->controller()->param('action');
		
		if (strpos($path, '/')) {
			$path	= str_replace('/', '.', $path);
			$path	= "{$ns}.views.{$path}";
		} else {
			$controller	= $this->controller()->param('controller');
			$path	= "{$ns}.views.{$controller}.$path";
		}
		
		if ($options['layout']) {
			$vars	= $this->tplVars();
			$layout	= $options['layout'];
			$layout	= "{$ns}.views.layouts.{$layout}";
			$layoutPath	= Loader::instance()->toPath($layout); 
			$content_for_layout	= $this->renderToString($path);
			
			include_once $layoutPath;
		} else {
			\Vzed\import($path);
		}
	}
	
	public function renderToString($template) {
		ob_start();
		\Vzed\import($template);
		return ob_get_clean();
	}
	
}

?>