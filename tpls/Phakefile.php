<?php 
return <<<EOF
<?php
require_once "public" . DIRECTORY_SEPARATOR . "defines.php";

$speedyPhake = VENDOR_PATH . DS . 'speedy-php' . DS . 'framework' . DS . 'tasks' . DS . 'Phakefile.php';
if (!file_exists($speedyPhake)) {
	trigger_error('Missing SpeedyPHP Phakefile. Try running composer install first.');
	return;
}

require_once $speedyPhake;

EOF;
?>
