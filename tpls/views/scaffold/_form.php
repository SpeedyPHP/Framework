<?php 
$tpl	= <<<EOF
<?php echo \$this->formFor(\$this->{$modelLc}, null, function(\$f) { ?>

	<?php if (\$this->{$modelLc}->errors && \$this->{$modelLc}->errors->count()): ?>
		<div id="error_explanation">
			<?php echo \$f->element('h2', "{\$this->pluralize(\$this->{$modelLc}, 'error')} prohibited this {$modelLc} from beign saved:"); ?>
		</div>
		<ul>
			<?php \$this->{$modelLc}->errors->each(function(\$error) { ?>
				<li><?php echo \$error; ?></li>
			<?php }); ?>
		</ul>
	<?php endif; ?>
EOF;
$tpl	.= "\n\r";

foreach ($columns as $column) {
	if ($column->name == 'id') {
		continue;
	}
	
	$tpl	.= 
		"\t<div class=\"field\">\n" .
		"\t\t<?php echo \$f->label(\"{$column->name}\"); ?>\n" .
		"\t\t<?php echo \$f->textField(\"{$column->name}\"); ?>\n" .
		"\t</div>\n";
}

$tpl	.= <<<EOF
	<div class="actions">
		<?php echo \$f->submit('Save'); ?>
	</div>
	
<?php }); ?>
EOF;
return $tpl;
?>
