<?php 
return <<<EOF
<?php
namespace {$namespace}\Controllers;

\Vzed\import('{$lcNamespace}.controller.application');

use \{$namespace}\Application;

class {$controller} extends Application {

	{$actions}

}

?>
EOF;
?>