<?php 
return <<<EOF
<?php
namespace $namespace\Config;

\Vzed\import('vzed.router.draw');
use \Vzed\Router\Draw as VzedDraw;

class Routes extends VzedDraw {

	public function draw() {
	
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