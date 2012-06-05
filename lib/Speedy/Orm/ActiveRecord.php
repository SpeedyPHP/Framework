<?php 
namespace Speedy\Orm;

use \Speedy\Orm\Base;
use \Speedy\Loader;

class ActiveRecord extends Base {

	public static function setup(\Speedy\Config $config) {
		$connections	= $config->dbStrings();
		
		\ActiveRecord\Config::initialize(function($conf) use ($connections) {
			$conf->set_connections($connections);
		});
	}
		
}

?>