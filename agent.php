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

// Decide which agents to load:
exec("which gpsd", $gpsd_present);
exec("which nmap", $nmap_present);
exec("which hcitool", $bluez_present);
exec("ifconfig wlan0", $has_wifi);
if(count($bluez_present) == 1) {
  require_once("agents/bluetooth-le-scan.php");
}
require_once("agents/heartbeat.php");
if(count($gpsd_present) == 1) {
  require_once("agents/location.php");
}
if(count($nmap_present) == 1) {
  require_once("agents/netscan.php");
}
require_once("agents/temperature.php");
if(count($has_wifi) > 0) {
  require_once("agents/wifi-health.php");
}

echo "Starting Agent Loop\n";
$loop->run();
