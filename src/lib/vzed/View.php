<?php 
namespace Vzed;

use \Vzed\Loader;
use \Vzed\Config;
use \Vzed\Utility\Inflector;
use \Vzed\Http\Exception as HttpException;
use \Vzed\Singleton;

class View extends Singleton {
	
	/**
	 * Template variables
	 * @var array
	 */
	protected $_vars;
	
	/**
	 * Render the template
	 * @param string $template
	 */
	public function render($file, $options, $vars = array(), $data = array(), $ext = 'html') {
		$viewPaths	= Loader::instance()->path('views');
		$renderers	= Config::instance()->renderers();
		
		foreach ($renderers as $type => $renderer) {			
			foreach ($viewPaths as $path) {
				$fullPath	= $path . DS . $file . ".{$type}.{$ext}";
				
				if (!file_exists($fullPath)) {
					continue;
				}
				
				$class	= Loader::instance()->toClass($renderer);
				$obj	= new $class($fullPath, $options);
				$obj->setVars($vars)->setData($data)->render();
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Find renderer from fileNmae
	 * @param string $fileName
	 * @return string
	 */
	public function renderer($fileName) {
		$fileNameArr	= explode('.', $fileName);
		return $fileNameArr[1];
	}
	
}

?>