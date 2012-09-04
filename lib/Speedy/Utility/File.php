<?php
namespace Speedy\Utility;


defined('DS') or define('DS', DIRECTORY_SEPARATOR);

class File {

	
	/**
	 * Copy convience method
	 * @param string $source
	 * @param string $dest
	 * @param resource $context
	 * @return bool
	 */
	public static function cp($source, $dest, $resource = null) {
		return copy($source, $dest, $resource);
	}
	
	/**
	 * Copy recursively
	 * @param string $source
	 * @param string $dest
	 * @param resource $context
	 * @return void
	 */
	public static function cp_r($source, $dest, $resource = null) {
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
	
	/**
	 * Delete dir recursively
	 * @param string $dirname
	 * @param resource $context
	 * @return bool
	 */
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