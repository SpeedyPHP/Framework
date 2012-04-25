<?php 
namespace Speedy\Orm;

use \Speedy\Orm\Base;
use \Speedy\Loader;

Loader::instance()->registerNamespace('active_record', SPEEDY_PATH . DS . 'active_record');
\Speedy\import('active_record.utils');
\Speedy\import('active_record.exceptions');

class ActiveRecord extends Base {

	public static function setup(\Speedy\Config $config) {
		$connections	= $config->dbStrings();
		
		\ActiveRecord\Config::initialize(function($conf) use ($connections) {
			$conf->set_connections($connections);
		});
	}
		
}

?>