<?php 
return <<<EOF
<?php

use \Speedy\Loader;
use \Speedy\Session;

class App extends \Speedy\App {

	protected \$_name = "{$namespace}";


	protected function initApp() {
		Session::start();
	}
	
}

?>
EOF;
?>
