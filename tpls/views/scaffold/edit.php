<?php 
return <<<EOF
<h1>Editing {$modelLc}</h1>

<?php \$this->render("form"); ?>

<?php \$this->linkTo('Show', \$this->{$modelLc}_path(\$this->{$modelLc}->id)); ?> 
|
<?php \$this->linkTo('Back', \$this->{$modelPlural}_url()); ?>
EOF;
?>
