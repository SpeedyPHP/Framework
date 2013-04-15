<?php 
return <<<EOF
<h1>Editing {$modelLc}</h1>

<?php echo \$this->render("form"); ?>

<?php echo \$this->linkTo('Show', \$this->{$modelLc}_path(\$this->{$modelLc}->id)); ?> 
|
<?php echo \$this->linkTo('Back', \$this->{$modelPlural}_url()); ?>
EOF;
?>