<?php 
return <<<EOF
	/**
	 * GET /posts/1
	 */
	public function show() {
		\$this->{$modelPlural}	= {$modelName}::find(\$this->params('id'));
		
		\$this->respondTo(function(\$format) {
			switch(\$format) {
				case 'html':
				default:
					// render html
					break;
			}
		});
	}
EOF;
?>