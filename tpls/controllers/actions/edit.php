<?php 
return <<<EOF
	/**
	 * GET /posts/1/edit
	 */
	public function edit() {
		\$this->{$modelLc}	= {$modelName}::find(\$this->params('id'));
	}
EOF;
?>