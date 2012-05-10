<?php 
return <<<EOF
	/**
	 * GET /posts
	 */
	public function index() {
		\$this->{$modelPlural}	= {$modelName}::all();
		
		\$this->respondTo(function(&\$format) {
			\$format->html; // Render per usual
			\$format->json	= function() {
				\$this->render(array( 'json' => \$this->{$modelPlural} ));
			};
		});
	}
EOF;
?>