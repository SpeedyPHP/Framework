<?php 
namespace Vzed;

use \Vzed\Loader;

class View {
	
	/**
	 * Reference to controller
	 * @var \Vzed\Controller
	 */
	private $_controller;
	
	/**
	 * Path to the template
	 * @var string
	 */
	private $_templatePath;
	
	/**
	 * Options for template
	 * @var array
	 */
	private $_options;
	
	/**
	 * Template variables
	 * @var array
	 */
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
		
		if (!file_exists(Loader::instance()->toPath($path))) {
			// TODO: No view found
		}
		
		if ($options['layout']) {
			$vars	= $this->tplVars();
			$layout	= $options['layout'];
			$layout	= "{$ns}.views.layouts.{$layout}";
			$layoutPath	= Loader::instance()->toPath($layout); 
			$content_for_layout	= $this->renderToString($path);
			
			if (!file_exists($layoutPath)) {
				// TODO: No layout found
			}
			
			include_once $layoutPath;
		} else {
			\Vzed\import($path);
		}
		
		// TODO: Throw unknown error exception
	}
	
	/**
	 * Renders the template to a string
	 * @param string $template path to template
	 * @return string
	 */
	public function renderToString($template) {
		ob_start();
		\Vzed\import($template);
		return ob_get_clean();
	}
	
}

?>