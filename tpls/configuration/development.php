<?php 
return <<<EOF
<?php
App::instance()->configure(function(\$conf) {

	// Turn on short links
	// \$conf->set('short_links', true);
	
	\$conf->set('Security.salt', 'salt'); // Change this to your liking
	\$conf->set('logger', '\\Speedy\\Logger\\Console');
	\$conf->set('Session.manager', '\\Speedy\\Session\\File');
	\$conf->set('Config.manager', '\\Speedy\\Cache\\File');
	
	date_default_timezone_set('America/Los_Angeles');
	
	\Speedy\Loader::instance()->registerNamespace("\{\$this->ns()}.lib", LIB_PATH);
	//import('active_record.utils');
	//import('active_record.exceptions');
	//import('active_record.logger.runtime');
	
	\$connections	= \$this->config()->dbStrings();
	\ActiveRecord\Config::initialize(function(\$conf) use (\$connections) {
		\$conf->set_connections(\$connections);
		\$conf->set_logging(true);
		\$conf->set_logger(\ActiveRecord\Logger\Runtime::instance());
		\$conf->set_default_connection('development');
	});
	
	\$conf->addRenderer('php', 'speedy.view.php');
});
?>
EOF;
?>
