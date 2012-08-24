<?php 
return <<<EOF
{
	"development": {
		"adapter"	: "mysql",
		"encoding"	: "utf8",
		"reconnect"	: "false",
		"database"	: "db_development",
		"pool"	: 5,
		"username"	: "root",
		"password"	: "",
		"host"	: "localhost"
	},

	"test" : {
		"adapter"	: "mysql",
		"encoding"	: "utf8",
		"reconnect"	: "false",
		"database"	: "db_test",
		"pool"	: 5,
		"username"	: "root",
		"password"	: "",
		"host"	: "localhost"
	},
	
	"production" : {
		"adapter"	: "mysql",
		"encoding"	: "utf8",
		"reconnect"	: "false",
		"database"	: "db_production",
		"pool"	: 5,
		"username"	: "root",
		"password"	: "",
		"host"	: "localhost"
	}	
}
EOF;
?>