<?php 
return <<<EOF
<?php
namespace {$namespace}\Controllers;

use \\{$namespace}\Controllers\Application;
use \\{$namespace}\Models\\{$modelName};

class {$controller} extends Application {

	{$actions}

}

?>
EOF;
?>