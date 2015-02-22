<?php
chdir(dirname(__FILE__));

if(!file_exists('./vendor/autoload.php')){
  die("You need to run <em>php composer.phar update</em> in the root directory.");
}
require_once("vendor/autoload.php");
require_once("config/env.php");
require_once("config/database.php");
require_once("config/eventsd.php");
$i = 0;

$loop = React\EventLoop\Factory::create();

require_once("agents/netscan.php");
require_once("agents/temperature.php");

$loop->run();
