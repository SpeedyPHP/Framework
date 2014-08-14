<?php 
$content = <<<EOF
<?php
namespace {$namespace}\Controllers{$controllerNs};


EOF;

if (isset($modelName)) $content .= "\nuse {$namespace}\Models\\{$modelName};\n";
$content .= <<<EOF
class {$controller} extends Application {
	{$actions}
}


EOF;


return $content; 
?>
