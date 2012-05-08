<?php 
return <<<EOF
<?php
	/** 
	 * DELETE /posts/1
	 */
	public function destroy() {
		\$this->{$modelLc} = {$modelName}::find(\$this->params('id'));
		\$this->{$modelLc}->destroy();
		
		\$this->respondTo(function(\$format) {
			\$format->html = function() { \$this->redirectTo(\$this->{$modelPlural}_url()); };
		});
	}
?>
EOF;
?>