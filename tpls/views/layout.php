<?php 
return <<<EOF
<!DOCTYPE html>
<html>
<head>
	<title>{$namespace}</title>
</head>
<body>
	<?php \$this->yield(); ?>
</body>
</html>
EOF;
?>
