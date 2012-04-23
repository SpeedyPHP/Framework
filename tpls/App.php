<?php 
return <<<EOF
<?php
require_once('Loader.php');
require_once('Routes.php');

\Vzed\import('vzed.app');
use \Vzed\Loader;

class App extends \Vzed\App {

	protected \$_name = "{$namespace}";


	protected function initApp() {
		
	}
	
}

?>
EOF;
?>