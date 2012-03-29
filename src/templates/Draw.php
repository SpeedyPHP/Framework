<?php 
return <<<EOF
<?php
namespace $namespace\Config;

import('vzed.router.routes.draw');
use \Vzed\Router\Routes\Draw as VzedDraw;

class Routes extends VzedDraw {

	protected function draw() {
	
		// Resource routing example:
		// \$this->resources('posts');
		
		// Match example:
		// \$this->match(array( '/posts/edit' => 'posts#edit', 'on' => 'GET' ));
		
		// Root example:
		// \$this->rootTo('posts#show', array( 'id' => 1 ));
	
	}

} 

?>
EOF;
?>