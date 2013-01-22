<?php 
namespace Speedy\Middleware;

use \Speedy\Config;
use \Speedy\Loader;
use \Speedy\Router;
use \Speedy\Middleware\Base as MiddlewareBase;


class Asset extends MiddlewareBase {

	public $types	= array(
		'javascript',
		'style',
		'images'
	);
	
	public $path;
	
	private $_debug	= false;
	
	
	
	public function call() {
		if ($this->has($this->request()->scriptName()) !== false) {
			$this->render();
			exit;
		} elseif ($this->has($this->request()->uri()) !== false) {
			$this->render();
			exit;
		} elseif ($this->has($this->request()->originalUrl()) !== false) {
			$this->render();
			exit;
		}
		
		$this->next()->call();
	}
	
	public function request() {
		return Router::instance()->request();
	}
	
	/**
	 * Get asset if exists
	 * @param string $asset
	 * @return mixed, false on failure
	 */
	public function has($asset) {
		$paths	= Loader::instance()->path('assets');
		
		$match = false;
		foreach ($paths	as $path) {
			foreach ($this->types as $type) {
				$file	= $path . DS . $type . $asset;

				if (!file_exists($file) || is_dir($file)) {
					continue;
				}
				
				if (count($this->path) > 0) {
					continue;
				}
					
				$this->path	= $file;
				$match = true;
				break(2);
			}
		}
		
		return $match;
	}
	
	public function path() {
		return $this->path;
	}
	
	public function render() {
		$path	= pathinfo($this->path);
		$sprocket = new \Speedy\Sprocket\Sprocket($path['basename'], array(
			'debugMode' => (isset($_GET['debug'])) ? (bool) $_GET['debug'] : $this->debug(),
			'assetFolder'	=> $path['dirname'] . DS,
			'cacheFolder'	=> TMP_PATH . DS . 'assets'
		));
	
		
		// change base folder based on extension
		switch ($sprocket->fileExt)
		{
			case 'css':
				$sprocket->setContentType('text/css')->setBaseFolder('/css');
				break;
		
			default: case 'js':
				$sprocket->setBaseFolder('/javascript');
				break;
		}
		
		// tada!
		$sprocket->render();
	}
	
	public function debug() {
		return $this->_debug;
	}
	
	private function setPath($value) {
		$this->path	= $value;
		return $this;
	} 
	
}

?>