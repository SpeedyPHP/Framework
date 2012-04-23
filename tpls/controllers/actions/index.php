<?php 
return <<<EOF
	/**
	 * GET /posts
	 */
	public function index() {
		\$this->{$modelPlural}	= {$modelName}::all();
		
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