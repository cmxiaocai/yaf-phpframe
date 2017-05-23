<?php
date_default_timezone_set("Asia/Shanghai");
define('ROOTDIRECTORY_PATH', dirname(dirname(__FILE__)));
define('APPLICATION_PATH', ROOTDIRECTORY_PATH.'/application');
define('APPLICATION_ENVIRONMENT', isset($_SERVER['ENVIRONMENT']) ? strtolower($_SERVER['ENVIRONMENT']) : 'product' );
#define('APPLICATION_ENVIRONMENT', 'local');
require_once "../vendor/autoload.php";
$application = new Yaf\Application( APPLICATION_PATH."/config/application.ini", APPLICATION_ENVIRONMENT);
$application->bootstrap()->run();
