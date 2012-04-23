<?php 
return <<<EOF
	/**
	 * GET /posts/1/edit
	 */
	public function edit() {
		\$this->{$lcModel}	= {$modelName}::find(\$this->params('id'));
		
	}
EOF;
?>