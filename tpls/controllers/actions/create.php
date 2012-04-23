<?php 
return <<<EOF
	/**
	 * POST /posts
	 */
	public function create() {
		\$this->{$lcModel}	= new {$modelName}(\$this->params('{$lcModel}'));
		
		\$this->respondTo(function(\$format) {
			if (\$this->{$lcModel}->save()) {
				switch(\$format) {
					case 'html':
					default:
						// render html
						break;
				}
			} else {
				switch(\$format) {
					case 'html':
					default:
						// render html
						break;
				}
			}
		});
	}
EOF;
?>