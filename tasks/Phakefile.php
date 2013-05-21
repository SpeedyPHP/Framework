<?php 
defined("STDOUT") or define("STDOUT", fopen("php://stdout", "w"));

App::instance();
use \Speedy\Utility\Inflector;
use \ActiveRecord\Connection;
use \ActiveRecord\SchemaMigration;


group('db', function() {
	desc('seeds the database');
	task('seed', function() {
		$connection	= Connection::instance();
		
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
			
			
			$obj	= new $class(Connection::instance());
			if ($obj->migrated()) {
				continue;
			}
			
			output("===================================================");
			output("Starting Migration for $class");
			output("===================================================");
			output();
			
			$obj->runUp();
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
		
		$last	= SchemaMigration::last();
		$glob	= ROOT . DS . 'db' . DS . 'migrate' . DS . $last->version . '*.php';
		$rollback	= false;
		
		foreach (glob($glob) as $migration) {
			require_once $migration;
			
			$info	= pathinfo($migration);
			$file	= $info['filename'];
			$fileArr= explode('_', $file);
			$version= array_shift($fileArr);
			$class	= Inflector::camelize(implode('_', $fileArr));
			
			$obj	= new $class(Connection::instance());
			if (!$obj->migrated()) {
				continue;
			}
			
			output('================== Rolling Back ===================');
			output();
			$obj->runDown();
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

desc('print out currently configured routes');
task('routes', function() {
	App::instance()->bootstrap();
	$router = App::instance()->router();
	
	$table = new Console_Table();
	$table->setHeaders(['Helper','Method','Format','Params']);
	foreach ($router->routes() as $route) {
		$options = $route->options();
		$table->addRow([
				$route->name(),
				(empty($options['on'])) ? 'GET' : $options['on'],
				$route->format(),
				json_encode([
							'controller'=> $options['controller'],
							'action'	=> $options['action']
						])
			]);
	}
	
	output($table->getTable());
});


group('tmp', function() {
	desc('Flushs the cache');
	task('flush', function() {
		Speedy\Utility\File::rm_rf(TMP_PATH . DS . 'cache');
	});
});

function output($str = "") {
	fwrite(STDOUT, $str . "\n");
}


?>