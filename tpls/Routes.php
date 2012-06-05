<?php 
return <<<EOF
<?php
namespace $namespace\Config;


use \Speedy\Router\Draw as SpeedyDraw;

class Routes extends SpeedyDraw {

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