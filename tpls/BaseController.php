<?php 
$content = <<<EOF
<?php
namespace {$namespace}\Controllers\{$controllerNs};

use \\{$namespace}\Controllers\Application;
EOF;

if ($modelName) $content .= "\nuse \\{$namespace}\Models\\{$modelName};\n";
$content .= <<<EOF
class {$controller} extends Application {
	{$actions}
}

?>
EOF;


return $content; 
?>