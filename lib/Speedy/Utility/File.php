<?php
namespace Speedy\Utility;


defined('DS') or define('DS', DIRECTORY_SEPARATOR);

class File {

	
	public static function cp($source, $dest, $resouce = null) {
		return copy($source, $dest, $resource);
	}
	
	public static function cp_r($source, $dest, $resouce = null) {
		$dir = opendir($source);
		@mkdir($dest);
		while(false !== ($file = readdir($dir))) {
			if (($file != '.') && ($file != '..')) {
				if (is_dir($source . DS . $file)) {
					self::cp_r($source . DS . $file, $dest . DS . $file, $resource);
				}
				else {
					self::cp($source . DS . $file, $dest . DS . $file, $resource);
				}
			}
		}
		closedir($dir);
	}
	
	public static function rm_rf($dirname, $context = null) {
		foreach(glob($dirname . DS . '*') as $file) {
			if(is_dir($file))
				self::rm_rf($file, $context);
			else
				unlink($file, $context);
		}
		
		return rmdir($dirname, $context);
	}
	
}
?>