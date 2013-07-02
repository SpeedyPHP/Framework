<?php 
return <<<TPL
<h1>New {$modelLc}</h1>

<?php \$this->render('form'); ?>

<?php \$this->linkTo('Back', \$this->{$modelPlural}_url()); ?>
TPL;
?>
