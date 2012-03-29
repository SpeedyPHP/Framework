<?php 
return <<<EOF
<?php
\Vzed\import('vzed.app');

use \Vzed\Loader;

class App extends \Vzed\App {

	protected function initApp() {
		\$loader = Loader::getInstance();
		\$loader->registerNamespace($lcNamespace, APP_PATH);
		\$loader->registerNamespace({$lcNamespace}.config, CONFIG_PATH);
		
		
	}
	
}

?>
EOF;
?>