<?php

include_once(dirname(__file__). '/../.././../basic_config.php');
// ---------------------------------------------------------
//define('DBHOST', 'kmcse_trade');
//define('DBUSER', 'bitcoin');
//define('DBPASS', 'b$k^39@34');
//define('DBNAME', 'bitcoin');
define('DBHOST', $db_host);
define('DBUSER', $db_name);
define('DBPASS', $db_pass);
define('DBNAME', $db_user);
// ---------------------------------------------------------

 define('DEBUG', false);// set to false when done testing
 $app_name = "phpJobScheduler";
 $phpJobScheduler_version = "3.7";
