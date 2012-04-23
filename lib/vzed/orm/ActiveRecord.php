<?php 
namespace Vzed\Orm;

use \Vzed\Orm\Base;
use \Vzed\Loader;

Loader::instance()->registerNamespace('active_record', VZED_PATH . DS . 'active_record');
\Vzed\import('active_record.utils');
\Vzed\import('active_record.exceptions');

class ActiveRecord extends Base {

	public static function setup(\Vzed\Config $config) {
		$connections	= $config->dbStrings();
		
		\ActiveRecord\Config::initialize(function($conf) use ($connections) {
			$conf->set_connections($connections);
		});
	}
		
}

?>