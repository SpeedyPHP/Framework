<?php 
namespace Speedy;

use \Speedy\Config;
use \Speedy\Loader;
use \Speedy\Singleton;


class Asset extends Singleton {

	public $types	= array(
		'javascript',
		'style'
	);
	
	public $path;
	
	private $_debug	= false;
	
	
	
	/**
	 * Get asset if exists
	 * @param string $asset
	 * @return mixed, false on failure
	 */
	public function has($asset) {
		$paths	= Loader::instance()->path('assets');
		
		foreach ($paths	as $path) {
			foreach ($this->types as $type) {
				$file	= $path . DS . $type . $asset;
				if (!file_exists($file)) {
					continue;
				}
				
				if (count($this->path) > 0) {
					continue;
				}
					
				$this->path	= $file;
				break(2);
			}
		}
		
		return (strlen($this->path)) ? true : false;
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