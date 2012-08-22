<?php 
$tpl	= <<<EOF
<?php \$this->formFor(\$this->{$modelLc}, null, function(\$f) { ?>

	<?php if (\$this->{$modelLc}->errors && \$this->{$modelLc}->errors->count()): ?>
		<div id="error_explanation">
			<?php element('h2', "{\$this->pluralize(\$this->{$modelLc}, 'error')} prohibited this {$modelLc} from beign saved:"); ?>
		</div>
		<ul>
			<?php foreach(\$this->{$modelLc}->errors as \$error): ?>
				<li><?php echo \$error; ?></li>
			<?php endforeach; ?>
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
		"\t\t<?php \$f->label(\"{$column->name}\"); ?>\n" .
		"\t\t<?php \$f->textField(\"{$column->name}\"); ?>\n" .
		"\t</div>\n";
}

$tpl	.= <<<EOF
	<div class="actions">
		<?php \$f->submit('Save'); ?>
	</div>
	
<?php }); ?>
EOF;
return $tpl;
?>