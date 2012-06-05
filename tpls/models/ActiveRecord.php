<?php 
return <<<EOF
<?php
namespace {$namespace}\Models;

use \Speedy\Model\ActiveRecord\Base;

class {$model} extends Base {
}

?>
EOF;
?>