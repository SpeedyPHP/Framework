<?php 
return <<<EOF
	/**
	 * PUT /posts/1
	 */
	public function update() {
		\$this->{$modelLc}	= {$modelName}::find(\$this->params('id'));
		
		\$this->respondTo(function(\$format) {
			if (\$this->{$modelLc}->update_attributes(\$this->params('{$modelLc}')) {
				\$format->html = function() {
					\$this->redirectTo(\$this->{$modelLc}, array("notice" => "{$modelName} was successfully updated."));
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