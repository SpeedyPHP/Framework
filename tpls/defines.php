<?php 
return <<<EOF
<?php
defined('SPEEDY_ENV')  or define("SPEEDY_ENV", (getenv("SPEEDY_ENV")) ? getenv('SPEEDY_ENV') : 'development');
defined('DS') or define('DS', DIRECTORY_SEPARATOR); 			// Alias directory separator as DS
defined('ROOT') or define('ROOT', dirname(dirname(__FILE__)));	// Define the root directory of App
defined('PUBLIC_DIR') or define('PUBLIC_DIR', dirname(__FILE__));		// Define path to the public directory
defined('APP_PATH') or define('APP_PATH', ROOT . DS . 'app');	// Define path to the app directory
defined('CONFIG_PATH') or define('CONFIG_PATH', ROOT . DS . 'config');	// Define path to the config directory
defined('LIB_PATH') or define('LIB_PATH', ROOT . DS . 'lib');	// Define path to the lib directory
defined('TMP_PATH') or define('TMP_PATH', ROOT . DS . 'tmp');	// Define path to the tmp directory
defined('UPLOAD_PATH') or define('UPLOAD_PATH', TMP_PATH . DS . 'uploads');
defined('VENDOR_PATH') or define('VENDOR_PATH', ROOT . DS . 'vendor');
defined('PUBLIC_UPLOADS_DIR') or define('PUBLIC_UPLOADS_DIR', PUBLIC_DIR . DS . 'uploads');
//defined('SPEEDY_PATH') or define('SPEEDY_PATH', VENDOR_PATH . DS . 'SpeedyPHP' . );

defined('MODULES_PATH') or define('MODULES_PATH', ROOT . DS . 'modules'); // Not part of Speedy

if (function_exists('ini_set') && 
	ini_set('include_path', APP_PATH . PATH_SEPARATOR . ini_get('include_path'))) {
} 


require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

if (!include(CONFIG_PATH . DS . 'App.php')) {
	trigger_error("Could not find App class for current application, please check that app file is in CONFIG_PATH/App.php");
}
EOF;
?>
