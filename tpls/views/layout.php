<?php 
return <<<EOF
<!DOCTYPE html>
<html>
<head>
	<title>{$namespace}</title>
</head>
<body>
	<?php echo \$content_for_layout; ?>
</body>
</html>
EOF;
?>