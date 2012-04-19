<?php 
return <<<EOF
<?php
namespace {$namespace}\Controllers;

use \{$namespace}\Controllers\Application;

class {$controller} extends Application {

	{$actions}

}

?>
EOF;
?>