<?php
use \Speedy\Utility\Inflector;

$tpl	= <<<TPL
<p id="notice"></p>
TPL;

$tpl	.= "\n\r";
foreach ($columns as $column) {
	$title	= Inflector::titleize($column->name);
	$tpl	.= <<<TPL
<p>
	<b>{$title}</b>
	<?php echo \${$modelLc}->{$column->name}; ?>
</p>
TPL;
	$tpl	.= "\n";
}

$tpl	.=	"\r";
$tpl	.= <<<TPL
<?php echo \$this->linkTo('Edit', \$this->edit_{$modelLc}_path(\$this->{$modelLc}->id)); ?>
<?php echo \$this->linkTo('Back', \$this->{$modelPlural}_path()); ?>
TPL;

return $tpl;
?>