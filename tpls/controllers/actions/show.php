<?php 
return <<<EOF
	/**
	 * GET /posts/1
	 */
	public function show() {
		\$this->{$modelLc}	= {$modelName}::find(\$this->params('id'));
		
		\$this->respondTo(function(&\$format) {
			\$format->html; // show.php.html
			\$format->json	= function() {
				\$this->render(array( 'json' => \$this->{$modelLc} ));
			};
		});
	}
EOF;
?>