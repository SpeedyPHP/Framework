<?php 
defined("STDOUT") or define("STDOUT", fopen("php://stdout", "w"));

if (!include(CONFIG_PATH . DS . 'App.php')) {
	trigger_error("Could not find App class for current application, please check that app file is in CONFIG_PATH/App.php");
}

App::instance();
import('speedy.utility.inflector');

use \Speedy\Utility\Inflector;


group('db', function() {
	desc('seeds the database');
	task('seed', function() {
		$connection	= \ActiveRecord\Connection::instance();
		
		output('========== Attempting to Seed Database ============');
		try {
			$connection->transaction();
			$sql	= "CREATE TABLE schema_migrations (" .
						'`version` varchar(255) NOT NULL);';
			$connection->query($sql);
			
			$sql	= "CREATE UNIQUE INDEX `unique_schema_migrations` ON `schema_migrations` (`version`)";
			$connection->query($sql);
			
			$connection->commit();
			output("Database seeded");
		} catch (\Exception $e) {
			$connection->rollback();
			output();
			output($e);
		}
	});
	
	
	desc('run migrations');
	task('migrate', function() {
		$migrations	= ROOT . DS . 'db' . DS . 'migrate' . DS . '*.php';
		$migrated	= false;
		
		foreach (glob($migrations) as $migration) {
			require_once $migration;
			
			$info	= pathinfo($migration);
			$file	= $info['filename'];
			$fileArr= explode('_', $file);
			$version= array_shift($fileArr);
			$class	= Inflector::camelize(implode('_', $fileArr));
			
			
			$obj	= new $class(\ActiveRecord\Connection::instance());
			if ($obj->migrated()) {
				continue;
			}
			
			output("===================================================");
			output("Starting Migration for $class");
			output("===================================================");
			output();
			
			$obj->up();
			$log	= $obj->log();
			foreach ($log as $l) {
				output($l);
			}
			
			$migrated = true;
		}
		
		if ($migrated) {
			output();
			output("============= Successfully Completed ==============");
		}
	});
	
	desc('rollback one migration');
	task('rollback', function() {
		import('active_record.migration');
		
		$last	= \ActiveRecord\SchemaMigration::last();
		$glob	= ROOT . DS . 'db' . DS . 'migrate' . DS . $last->version . '*.php';
		$rollback	= false;
		
		foreach (glob($glob) as $migration) {
			require_once $migration;
			
			$info	= pathinfo($migration);
			$file	= $info['filename'];
			$fileArr= explode('_', $file);
			$version= array_shift($fileArr);
			$class	= Inflector::camelize(implode('_', $fileArr));
			
			$obj	= new $class(\ActiveRecord\Connection::instance());
			if (!$obj->migrated()) {
				continue;
			}
			
			output('================== Rolling Back ===================');
			output();
			$obj->down();
			$log	= $obj->log();
			foreach ($log as $l) {
				output($l);
			}
				
			$rollback = true;
		}
		
		if ($rollback) {
			output();
			output("=============== Rollback Completed ================");
		}
	});
});


function output($str = "") {
	fwrite(STDOUT, $str . "\n");
}


?>