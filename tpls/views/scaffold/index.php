<?php
use \Speedy\Utility\Inflector;

$tpl	= <<<TPL
<h1>Listing {$modelPlural}</h1>

<table>
	<tr>
TPL;

$tpl	.= "\n";
foreach($columns as $column) {
	$tpl	.= "\t\t<th>" . Inflector::titleize($column->name) . "</th>\n";
}
$tpl	.= <<<TPL
		<th></th>
		<th></th>
		<th></th>
	</tr>
	
	<?php echo \$this->{$modelPlural}->each(function(\${$modelLc}) { ?>
		<tr>
TPL;

$tpl	.= "\n";
foreach ($columns as $column) {
	$tpl	.= "\t\t\t<td><?php echo \${$modelLc}->{$column->name}; ?></td>\n";
}
$tpl	.= <<<TPL
			<td><?php echo \$this->linkTo('Show', \$this->{$modelLc}_path(\${$modelLc}->id)); ?></td>
			<td><?php echo \$this->linkTo('Edit', \$this->edit_{$modelLc}_path(\${$modelLc}->id)); ?></td>
			<td><?php echo \$this->linkTo('Destroy', \$this->{$modelLc}_path(\${$modelLc}->id), array( 'confirm' => 'Are you sure?', 'method' => 'delete' )); ?></td>
		</tr>
	<?php }); ?>
</table>
			
<br>
			
<?php echo \$this->linkTo("New {$modelPlural}", \$this->new_{$modelLc}_path()); ?>
TPL;

return $tpl;
?>