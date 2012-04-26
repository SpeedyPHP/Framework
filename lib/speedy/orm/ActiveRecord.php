<?php 
namespace Speedy\Orm;

use \Speedy\Orm\Base;
use \Speedy\Loader;

Loader::instance()->registerNamespace('active_record', getenv('ACTIVE_RECORD_PATH'));
import('active_record.utils');
import('active_record.exceptions');

class ActiveRecord extends Base {

	public static function setup(\Speedy\Config $config) {
		$connections	= $config->dbStrings();
		
		\ActiveRecord\Config::initialize(function($conf) use ($connections) {
			$conf->set_connections($connections);
		});
	}
		
}

?>