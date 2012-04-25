<?php 
return <<<EOF
<?php
require_once('Loader.php');
require_once('Routes.php');

import('vzed.app');
use \Speedy\Loader;

class App extends \Speedy\App {

	protected \$_name = "{$namespace}";


	protected function initApp() {
		
	}
	
}

?>
EOF;
?>