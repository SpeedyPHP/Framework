<?php 
return <<<EOF
<?php \$this->formFor(\$this->{$modelLc}, null, function(\$f) { ?>
	<?php if (count(\$this->{$modelLc}->errors)): ?>
		<div id="error_explanation">
			<?php element('h2', "{\$this->pluralize(\$this->{$modelLc}, 'error')} prohibited this {$modelLc} from beign saved:"); ?>
		</div>
		<ul>
			
		</ul>
	<?php endif; ?> 
<?php }); ?>
EOF;
?>