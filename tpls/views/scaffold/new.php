<?php 
return <<<TPL
<h1>New {$modelLc}</h1>

<?php echo \$this->render('form'); ?>

<?php echo \$this->linkTo('Back', \$this->{$modelPlural}_url()); ?>
TPL;
?>