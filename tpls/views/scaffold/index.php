<?php
use \Speedy\Utility\Inflector;

$tpl	= <<<TPL
<h1>Listing {$modelPlural}</h1>

<table>
	<tr>
TPL;

$tpl	.= "\n";
foreach($columns as $column) {
	$tpl	.= "\t\t<th>" . Inflector::titleize($column->name) . "<th>\n";
}
$tpl	.= <<<TPL
		<th></th>
		<th></th>
		<th></th>
	</tr>
	
	<?php \$this->{$modelPlural}->each(function(\${$modelLc}) { ?>
		<tr>
TPL;

$tpl	.= "\n";
foreach ($columns as $column) {
	$tpl	.= "\t\t\t<td><?php echo \${$modelLc}->{$column->name}; ?></td>\n";
}
$tpl	.= <<<TPL
			<td><?php \$this->linkTo('Show', \$this->{$modelLc}_path(\${$modelLc}->id)); ?></td>
			<td><?php \$this->linkTo('Edit', \$this->edit_{$modelLc}_path(\${$modelLc}->id)); ?></td>
			<td><?php \$this->linkTo('Destroy', \$this->{$modelLc}_path(\${$modelLc}->id)); ?></td>
		</tr>
	<?php }); ?>
</table>
			
<br>
			
<?php \$this->linkTo("New {$modelPlural}", \$this->new_{$modelLc}_path()); ?>
TPL;

return $tpl;
?>