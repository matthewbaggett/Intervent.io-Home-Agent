<?php
define("APP_ROOT", dirname(__FILE__));
chdir(APP_ROOT);

if(!file_exists('./vendor/autoload.php')){
  die("You need to run <em>php composer.phar update</em> in the root directory.");
}
require_once("vendor/autoload.php");
require_once("config/env.php");
require_once("config/database.php");
require_once("config/eventsd.php");
$i = 0;

$loop = React\EventLoop\Factory::create();

require_once("agents/bluetooth-le-scan.php");
require_once("agents/heartbeat.php");
require_once("agents/location.php");
require_once("agents/netscan.php");
require_once("agents/temperature.php");
require_once("agents/wifi-health.php");

echo "Starting Agent Loop\n";
$loop->run();
