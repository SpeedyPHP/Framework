<?php 
return <<<EOF
	/**
	 * POST /posts
	 */
	public function create() {
		\$this->{$modelLc}	= new {$modelName}(\$this->params('{$modelLc}'));
		
		\$this->respondTo(function(\$format) {
			if (\$this->{$modelLc}->save()) {
				\$format->html = function() {
					\$this->redirectTo(\$this->{$modelLc}, array("notice" => "{$modelName} was successfully created."));
				};
				\$format->json = function() {
					\$this->render(array( 'json' => \$this->{$modelLc} ));
				};
			} else {
				\$format->html = function() {
					\$this->render("new");
				};
				\$format->json = function() {
					\$this->render(array( 'json' => \$this->{$modelLc}->errors ));
				};
			}
		});
	}
EOF;
?>
