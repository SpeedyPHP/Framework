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
	public static function cp($source, $dest) {
		return copy($source, $dest);
	}
	
	/**
	 * Copy recursively
	 * @param string $source
	 * @param string $dest
	 * @param resource $context
	 * @return void
	 */
	public static function cp_r($source, $dest) {
		$dir = opendir($source);
		@mkdir($dest);
		while(false !== ($file = readdir($dir))) {
			if (($file != '.') && ($file != '..')) {
				if (is_dir($source . DS . $file)) {
					self::cp_r($source . DS . $file, $dest . DS . $file);
				}
				else {
					self::cp($source . DS . $file, $dest . DS . $file);
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
	public static function rm_rf($dirname) {
		foreach(glob($dirname . DS . '*') as $file) {
			if(is_dir($file))
				self::rm_rf($file);
			else
				unlink($file);
		}
		
		return rmdir($dirname);
	}
	
}
?>