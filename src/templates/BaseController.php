<?php 
return <<<EOF
<?php
namespace {$namespace}\Controllers;

\Vzed\import('vzed.controller');

use \Vzed\Controller;

class {$controller} extends Controller {

	{$actions}

}

?>
EOF;
?>