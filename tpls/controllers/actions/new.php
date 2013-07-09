<?php 
return <<<EOF
	/**
	 * GET /posts/new
	 */
	public function _new() {
		\$this->{$modelLc}	= new {$modelName}();
		
		\$this->respondTo(function(\$format) {
			\$format->html; // new.php.html
			\$format->json = function() {
				\$this->render(array( 'json' => \$this->{$modelLc} ));
			};
		});
	}
EOF;
?>
