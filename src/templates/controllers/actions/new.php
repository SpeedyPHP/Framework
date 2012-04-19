<?php 
return <<<EOF
	/**
	 * GET /posts/new
	 */
	public function new() {
		\$this->{$lcModel}	= new {$modelName}();
		
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